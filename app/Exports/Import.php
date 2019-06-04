<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/3
 * Time: 10:30
 */

namespace App\Exports;


use Maatwebsite\Excel\Concerns\ToArray;

class Import implements ToArray
{
    public function array(array $array)
    {
        return $array;
    }

}