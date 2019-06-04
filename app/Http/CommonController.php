<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/4
 * Time: 9:43
 */

namespace App\Http;


use App\Http\Controllers\Controller;

class CommonController extends Controller
{

    //判断是否是周末
    protected function checkWeekend($date){
        if((date('w',strtotime($date))==6) || (date('w',strtotime($date)) == 0)){
            return true;
        }
        return false;
    }
}