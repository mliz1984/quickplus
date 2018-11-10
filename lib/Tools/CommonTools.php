<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 7:37 PM
 */

namespace Lib\Tools;


class CommonTools{

    public static function toTimeZone($src, $from_tz = 'Asia/Shanghai', $to_tz = 'America/Montreal', $fm = 'Y-m-d H:i:s') {
        $datetime = new DateTime($src, new DateTimeZone($from_tz));
        $datetime->setTimezone(new DateTimeZone($to_tz));
        return $datetime->format($fm);
    }
    public static function getDataArray($src,$prefix,$blank=true,$subkey=true)
    {
        $search = "/^".$prefix."\.*/";
        $result = array();
        foreach ($src as $key=>$value)
        {
            if(preg_match($search,$key))
            {
                if($subkey)
                {
                    $key = substr($key,strlen($prefix));
                }
                if(is_string($value))
                {
                    $value =trim($value);
                }
                if($blank==true)
                {
                    $result[$key] = $value;
                }
                elseif(isset($value) && ((is_string($value)&&strlen($value)>0)||(is_array($value)&&count($value)>0)))
                {

                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }

    public static function processMapArray($array,$path="root")
    {
        $result = array();
        $temp = array();
        if(is_array($array)&&count($array)>0)
        {
            foreach($array as $key => $value)
            {
                if(!is_array($value))
                {
                    $temp[$key] = $value;
                }
                else
                {

                    $subresult = self::processMapArray($value,$path.".".$key);
                    $result = array_merge($result,$subresult);
                }
            }
        }
        $result[$path] = $temp;
        return $result;

    }
}