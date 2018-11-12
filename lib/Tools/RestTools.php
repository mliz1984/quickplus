<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 7:38 PM
 */

namespace Quickplus\Lib\Tools;


class RestTools
{
    public static function post($url,$data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER, true);
        $result =  curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}