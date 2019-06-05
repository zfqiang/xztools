<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/3
 * Time: 9:50
 */

namespace App\Http\Controllers;

use App\Http\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DakaController extends CommonController
{

    public function __construct()
    {
        set_time_limit(0);
    }

    public function index(Request $request){

        $type = $request->get('type', '');
        $page = $request->get('page', '');
        $viewData = [
            'type' => $type,
            'page' => $page,
        ];
        return view('daka.index', $viewData);
    }
    public function dakaData(Request $request){

        $name = $request->get('name', '');

        //查询人员部门数据
        if(empty($name)){
            $datas = DB::table('members_daka')->paginate(20);
        }else{
            $datas = DB::table('members_daka')->where('name', $name)->paginate(20);
        }


        $viewData = [
            'datas' => $datas,
            'name' => $name,
        ];
        return view('daka.data', $viewData);
    }
    public function memberData(Request $request){
        //查询人员部门数据
        if(empty($name)){
            $members = DB::table('members')->paginate(20);
        }else{
            $members = DB::table('members')->where('name', $name)->paginate(20);
        }

        $viewData = [
            'members' => $members,
        ];
        return view('daka.memberdata', $viewData);
    }

    //Excel文件导出功能
    public function exportData()
    {
        //查询打卡数据
        $dakas = DB::table('members_daka')
                        ->get(['name', 'date_time', 'department', 'sxw']);
        $dks = [];
        foreach ($dakas as $daka){
            $dks[$daka->department][$daka->name][] = [
                'date_time' => str_replace('/', '-', $daka->date_time),
                'sxw' => $daka->sxw
            ];
        }

        $path = storage_path() . '/template/template.xls';
        $spreadsheet = IOFactory::load($path);

        //---------------------设置周末单元格背景---------------

        //获取月份-当月总天数
        $ddk = DB::table('members_daka')->limit(1)->first(['date_time']);
        $yearMonth = date('Y-m', strtotime($ddk->date_time));
        $totalDay = date('t', strtotime($ddk->date_time));

        $startIndex = 8;
        $endIndex = 68;
        for ($i = 1; $i < $totalDay; $i++){
            //封装日期
            $date = $yearMonth .'-'. $i;
            if($this->checkWeekend($date)){
                //周末设置单元格背景色
                $day = date('d', strtotime($date));

                //上午格子
                $swgz = $startIndex + ($day - 1) * 2;
                //下午格子
                $xwgz = $swgz + 1;

                //设置单元格背景色
                $spreadsheet->getActiveSheet()->getStyleByColumnAndRow(1, $swgz, $endIndex, $swgz)
                    ->getFill()->setFillType(Fill::FILL_SOLID);
                $spreadsheet->getActiveSheet()->getStyleByColumnAndRow(1, $swgz, $endIndex, $swgz)
                    ->getFill()->getStartColor()->setARGB('FFF0B000');
                $spreadsheet->getActiveSheet()->getStyleByColumnAndRow(1, $xwgz, $endIndex, $xwgz)
                    ->getFill()->setFillType(Fill::FILL_SOLID);
                $spreadsheet->getActiveSheet()->getStyleByColumnAndRow(1, $xwgz, $endIndex, $xwgz)
                    ->getFill()->getStartColor()->setARGB('FFF0B000');
            }
        }

        //---------------------设置周末单元格背景---------------

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

        //部门索引
        $column1 = 3;
        $row1 = 6;

        //人员索引
        $column2 = 3;
        $row2 = 7;

        foreach ($dks as $department => $dk){
            //设置部门并合并对应单元格
            $last1 = $column1 + count($dk);
            $spreadsheet->getActiveSheet()->getCellByColumnAndRow($column1,$row1)->setValue($department);
            $spreadsheet->getActiveSheet()->mergeCellsByColumnAndRow($column1,$row1, $last1 - 1,$row1);
            $column1 = $last1;


            foreach ($dk as $name => $ddk){
                $times = array_column($ddk, 'date_time');
                $sxwArr = array_column($ddk, 'sxw', 'date_time');

                $spreadsheet->getActiveSheet()->getCellByColumnAndRow($column2,$row2)->setValue($name);

                // 使用给定的用户定义函数对数组排序--升序
                usort($times, function($time1, $time2)
                {
                    if (strtotime($time1) < strtotime($time2)){
                        return -1;
                    } else if (strtotime($time1) > strtotime($time2)){
                        return 1;
                    } else {
                        return 0;
                    }
                });

                //数据索引
                $row3 = 7;
                foreach ($times as $time){
                    $day = date('d', strtotime($time));
                    $hour = date('H', strtotime($time));
                    $val = date('H:i:s', strtotime($time));
                    $date = date('Y-m-d', strtotime($time));


                    $daotime = '正常';
                    if($sxwArr[$time] == 1){
                        //上午格子
                        $row3 = $startIndex + ($day - 1) * 2;
                        $daotime = $this->checkTsDate($department, $time, 1);

                    }else{
                        //下午格子
                        $row3 = $startIndex + ($day - 1) * 2 + 1;
                        $daotime = $this->checkTsDate($department, $time, 2);
                    }

                    //获取表格的背景色
                    $ARGB = $this->getARGB($daotime);
                    if(!empty($ARGB)){
                        $spreadsheet->getActiveSheet()->getStyleByColumnAndRow($column2, $row3, $column2, $row3)
                            ->getFill()->setFillType(Fill::FILL_SOLID);
                        $spreadsheet->getActiveSheet()->getStyleByColumnAndRow($column2, $row3, $column2, $row3)
                            ->getFill()->getStartColor()->setARGB($ARGB);
                    }
                    $spreadsheet->getActiveSheet()->getCellByColumnAndRow($column2, $row3)->setValue($val);

                    $row3++;
                }
                $column2++;
            }
        }

        $file_name = $yearMonth . '打卡数据统计.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');

        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
//        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
//        $writer->save('write.xls');
    }



    //Excel文件导入功能
    public function importData(Request $request){
        $file = $request->file('file');
        $reader = IOFactory::createReader('Xls');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file); //载入excel表格

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // 总行数

        $lines = $highestRow - 1;
        if ($lines <= 0) {
            exit('Excel表格中没有数据');
        }

        //查询成员人部门
        $members = DB::table('members')->get(['name', 'department']);
        $bms = [];
        foreach ($members as $member){
            $bms[$member->name] = $member->department;
        }

        //导入数据前，删除之前是数据
        DB::table('members_daka')->delete();
        DB::table('daka_normal')->delete();


        //插入特殊日期
//        $tsfile = $request->file('tsfile');
        $reader2 = IOFactory::createReader('Xls');
        $reader2->setReadDataOnly(TRUE);
        $spreadsheet2 = $reader->load($file); //载入excel表格
        $worksheet2 = $spreadsheet2->getSheet(1);
        $highestRow2 = $worksheet2->getHighestRow(); // 总行数
        $lines2 = $highestRow2 - 1;
        if ($lines2 > 0) {
            for ($row2 = 2; $row2 <= $highestRow2; $row2++) {
                $fading_time = $worksheet2->getCellByColumnAndRow(1, $row2)->getValue();
                $buban_time = $worksheet2->getCellByColumnAndRow(2, $row2)->getValue();
                if(!empty($fading_time)){
                    $data = [
                        'date' => $fading_time,
                        'type' => 1,
                    ];
                    DB::table('daka_normal')->insert($data);
                }

                if(!empty($buban_time)){
                    $data = [
                        'date' => $buban_time,
                        'type' => 2,
                    ];
                    DB::table('daka_normal')->insert($data);
                }
            }
        }

        //处理打卡数据
        for ($row = 2; $row <= $highestRow; $row++) {
            $name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
            $date_time = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
            if(!empty($name)){
                $date_time = str_replace('/', '-', $date_time);
                $date_time = date('Y-m-d H:i:s', strtotime($date_time));

                //处理特殊打卡记录
                $this->checkTsDakaData($name, $date_time, $bms);
            }
        }

        return redirect()->route('dakaindex',['type' => 'importData']);
    }

    //导入人员部门信息
    public function importMembers(Request $request)
    {
        $file = $request->file('mfile');
        $reader = IOFactory::createReader('Xls');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file); //载入excel表格

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // 总行数

        $lines = $highestRow - 1;
        if ($lines <= 0) {
            exit('Excel表格中没有数据');
        }

        //导入数据前，删除之前是数据
        DB::table('members')->delete();

        for ($row = 2; $row <= $highestRow; $row++) {
            $name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            $department = $worksheet->getCellByColumnAndRow(2, $row)->getValue();

            $data = [
                'name' => $name,
                'department' => $department,
            ];
            DB::table('members')->insert($data);
        }

        return redirect()->route('dakaindex',['type' => 'importMembers']);

    }


    //导出人员部门信息
    public function exportMembers()
    {

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(14);

        $sheet->setTitle('人员部门数据');
        $k = 1;
        $sheet->setCellValue('A'.$k, '姓名');
        $sheet->setCellValue('B'.$k, '部门');


        $members = DB::table('members')->get();
        foreach ($members as $member){
            $sheet->setCellValue('A'.$k, $member->name);
            $sheet->setCellValue('B'.$k, $member->department);
            $k++;
        }

        $file_name = "人员部门信息.xls";
        header('Content-Type:application/vnd.ms-excel');//告诉浏览器将要输出Excel03版本文件
        header('Content-Disposition: attachment;filename="' . $file_name . '"');//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');//禁止缓存
        $objWriter  = IOFactory::createWriter($spreadsheet, 'Xls');
        $objWriter->save('php://output');

        exit;

    }


    //更新导出的模板
    public function updateTemplate(){

        //统计各部门人数
        $datas = DB::table('members')->selectRaw('count(*) as count, department')
            ->groupBy('department')
            ->get();

        $path = storage_path() . '/template/template.xls';
        $spreadsheet = IOFactory::load($path);

        $column = 3;
        $row = 6;
        foreach ($datas as $data){
            $last = $column + intval($data->count);
            $spreadsheet->getActiveSheet()->getCellByColumnAndRow($column,$row)->setValue($data->department);
            $spreadsheet->getActiveSheet()->mergeCellsByColumnAndRow($column,$row, $last - 1,$row);
            $column = $last;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('write.xls');


    }
}