<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/4
 * Time: 9:43
 */

namespace App\Http;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Color;

class CommonController extends Controller
{

    private $sbtime = [
        '常规' => [
            'swcd' => '09:16:00',
            'swyzcd' => '09:30:00',
            'xwxb' => '18:00:00',
            'jb' => '20:00:00',
        ],
        '研发' => [
            'swcd' => '10:16:00',
            'swyzcd' => '10:30:00',
            'xwxb' => '19:00:00',
            'jb' => '21:00:00',
        ],
    ];

    //判断是否是周末
    protected function checkWeekend($date){
        if((date('w',strtotime($date))==6) || (date('w',strtotime($date)) == 0)){
            return true;
        }
        return false;
    }

    //判断处理特色日期
    public function checkTsDate($department, $time, $swx){
        //查询特殊日期
        $tsdatas = DB::table('daka_normal')->get();
        $fjdata = [];
        $bbdata = [];
        foreach ($tsdatas as $ts){
            if($ts->type == 1){
                $fjdata[] = $ts->date;
            }else{
                $bbdata[] = $ts->date;
            }
        }

        $date = date('Y-m-d', strtotime($time));
        $daotime = '正常';
        //判断是否是周末
        if($this->checkWeekend($date)){
            //判断周末（周六周日）是否需要补班，
            if(in_array($date, $bbdata)){
                //是的话判断流程是否迟到或者严重迟到
                $daotime = $this->checkSwChiDao($department, $time, $swx);
            }else{
                //不需要补班，直接判断为加班
                $daotime = '加班';
            }
            return $daotime;
        }else{
            //工作日（周一到周五）判断是否是假期
            if(in_array($date, $fjdata)){
                //是的话，直接判定为加班
                $daotime = '加班';
            }else{
                //不是，判断是否迟到或者严重迟到
                $daotime = $this->checkSwChiDao($department, $time, $swx);
            }
            return $daotime;
        }
    }


    //判断迟到 swx 1：上午  2：下午
    protected function checkSwChiDao($department, $time, $sxw = 1){
        $val = date('H:i:s', strtotime($time));
        $daotime = '正常';
        if($sxw == 1){
            //判断是否迟到
            if($department == "研发"){
                if($val >= $this->sbtime['研发']['swyzcd']){
                    $daotime = '严重迟到';
                }else if($val >= $this->sbtime['研发']['swcd']){
                    $daotime = '迟到';
                }
            }else{
                if($val >= $this->sbtime['常规']['swyzcd']){
                    $daotime = '严重迟到';
                }else if($val >= $this->sbtime['常规']['swcd']){
                    $daotime = '迟到';
                }
            }
            return $daotime;
        }else{
            //判断是否迟到
            $daotime = '正常';
            if($department == "研发"){
                if($val < $this->sbtime['研发']['xwxb']){
                    $daotime = '早退';
                }else if($val >= $this->sbtime['研发']['jb']){
                    $daotime = '加班';
                }
            }else {
                if($val < $this->sbtime['常规']['xwxb']){
                    $daotime = '早退';
                }else if($val >= $this->sbtime['常规']['jb']){
                    $daotime = '加班';
                }
            }
            return $daotime;
        }
    }

    //返回表格的ARGB
    public function getARGB($daotime){
        $color = null;
        switch ($daotime){
            case '迟到':
                $color = Color::COLOR_YELLOW;
                break;
            case '严重迟到':
                $color = Color::COLOR_RED;
                break;
            case '加班':
                $color = Color::COLOR_GREEN;
                break;
            case '早退':
                $color = Color::COLOR_BLUE;
                break;
            default :
                $color = null;
        }
        return $color;
    }

    public function checkTsDakaData($name, $date_time, $bms){
        $date = date('Y-m-d', strtotime($date_time));
        //查询特殊日期
        $tsdatas = DB::table('daka_normal')->get();
        $fjdata = [];
        $bbdata = [];
        foreach ($tsdatas as $ts){
            if($ts->type == 1){
                $fjdata[] = $ts->date;
            }else{
                $bbdata[] = $ts->date;
            }
        }

        //加班的-记录第一条和最后一条数据
        if(in_array($date, $fjdata)){
            $jbdaka = DB::table('members_daka')->where('name', $name)
                ->whereBetween('date_time', [$date . ' 00:00:00', $date . ' 12:59:59'])
                ->orderBy('date_time', 'asc')
                ->get();
            if(empty($jbdaka)){
                //第一条数据标记为上午数据
                $data = [
                    'name' => $name,
                    'date_time' => $date_time,
                    'department' => isset($bms[$name])?$bms[$name]:null,
                    'sxw' => 1,
                ];

                DB::table('members_daka')->insert($data);
            }else{
                if(count($jbdaka) == 1){
                    //有一条数据判断打卡时间是否大于数据库数据的时间，如果大于，标记为下午数据，反之修改数据库第一条为下午数据，新读取的为上午数据
                    if(strtotime($jbdaka[0]->date_time) > strtotime($date_time)){
                        $data = [
                            'name' => $name,
                            'date_time' => $date_time,
                            'department' => isset($bms[$name])?$bms[$name]:null,
                            'sxw' => 2,
                        ];

                        DB::table('members_daka')->insert($data);
                    }else{
                        DB::table('members_daka')->where('id', $jbdaka[0]->id)->update(['sxw' => 2]);
                        $data = [
                            'name' => $name,
                            'date_time' => $date_time,
                            'department' => isset($bms[$name])?$bms[$name]:null,
                            'sxw' => 1,
                        ];

                        DB::table('members_daka')->insert($data);
                    }
                }else{
                    $swdata = $jbdaka[0];
                    if(strtotime($swdata->date_time) > strtotime($date_time)){
                        $data = [
                            'name' => $name,
                            'date_time' => $date_time,
                            'department' => isset($bms[$name])?$bms[$name]:null,
                            'sxw' => 1,
                        ];

                        DB::table('members_daka')->insert($data);
                        DB::table('members_daka')->delete($swdata->id);
                    }
                    $xwdata = $jbdaka[1];
                    if(strtotime($xwdata->date_time) < strtotime($date_time)){
                        $data = [
                            'name' => $name,
                            'date_time' => $date_time,
                            'department' => isset($bms[$name])?$bms[$name]:null,
                            'sxw' => 2,
                        ];

                        DB::table('members_daka')->insert($data);
                        DB::table('members_daka')->delete($swdata->id);
                    }
                }
            }

        }
    }

}