<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 7:37 PM
 */

namespace Quickplus\Lib\Tools;


class jqueryTools{
    public static function arrayToString($array)
    {
        $result = "";
        foreach($array as $key=>$value)
        {
            if(trim($result!=""))
            {
                $result .=",";
            }
            if(!is_array($value))
            {
                if(is_bool($value))
                {
                    $tmp = "false";
                    if($value)
                    {
                        $tmp = "true";
                    }
                    $result .= $key.":".$tmp."";
                }
                else if(is_string($value)&&!StringTools::isStartWith(trim(strtolower($value)),"function")&&!StringTools::isStartWith(trim(strtolower($value)),"["))
                {
                    $result .= $key.":'".$value."'";
                }
                else
                {
                    $result .= $key.":".$value;
                }
            }
            else
            {
                $result .= $key.":{".jqueryTools::arrayToString($value)."}";
            }

        }
        return $result;
    }
}
?>