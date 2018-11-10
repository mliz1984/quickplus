<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 7:36 PM
 */

namespace Lib\Tools;
use \Lib\Quickplus\Quickform\QuickFormConfig as QuickFormConfig;

class StringTools
{
    public static function parseExcelCellString($string)
    {
        $string = strtolower(trim($string));
        $result = null;
        $row = "";
        $col = "";
        if(strlen($string)>1&&ctype_lower(substr($string,0,1)))
        {
            $row = "";
            $col = "";
            for($i=0;$i<strlen($string);$i++)
            {
                $a = substr($string,$i,1);
                if(ctype_lower($a))
                {
                    $col .= $a;
                }
                else
                {
                    $row .= $a;
                }
            }
            $result = Array("row"=>$row,"col"=>$col);
        }
        return $result;

    }
    public static function escapeJsString($value)
    {
        $escapers = array("'", "\"", "&");
        $replacements = array("\\'", "\\\"", "\\&");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }
    public static function escapeJsonString($value)
    {
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }
    public static function getEncode($string)
    {
        return mb_detect_encoding($string, array('WINDOWS-1252','ASCII','GB2312','GBK','BIG5','UTF-8'));
    }
    public static function conv($string,$dst="UTF-8",$src=null)
    {

        if($src==null)
        {
            $src = self::getEncode($string);
        }

        if($src=="CP936"||$src=="ASCII"||$src="EUC-CN")
        {
            $src = QuickFormConfig::$encode;
        }

        $string =  iconv($src,$dst,$string);
        return  $string;
    }
    public static function getRandStr($len=6,$format='ALL')
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        switch($format){
            case 'ALL':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                break;
            case 'CHAR':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'NUMBER':
                $chars='0123456789';
                break;
        }
        mt_srand((double)microtime()*1000000*getmypid());
        $result='';
        while(strlen($result)<$len){
            $result.=substr($chars,(mt_rand()%strlen($chars)),1);
        }
        return $result;
    }

    public static function isStartWith($string,$begin)
    {
        $result = false;
        $length = strlen($begin);
        $tmp = substr($string,0,$length);
        if($tmp==$begin)
        {
            $result = true;
        }
        return $result;
    }

    public static function isEmpty($string)
    {
        $result = true;
        if(is_string($string)&&$string!=null&&trim($string)!="")
        {
            $result = false;
        }
        return $result;
    }

    public static function isEndWith($string,$end)
    {
        $result = false;
        $length = strlen($string)-strlen($end);
        $tmp = substr($string,$length);
        if($tmp==$end)
        {
            $result = true;
        }
        return $result;
    }

    public static function cutString($string,$start,$end,$with=true)
    {
        $result = null;
        if(strstr($string,$start)&&(strstr($string,$end)||!$end))
        {

            $startPoint = strpos($string,$start);
            $string  = substr($string,$startPoint+strlen($start));
            $endPoint = true;
            if($end)
            {
                $endPoint = strpos($string,$end);
            }
            if($endPoint)
            {
                if($end)
                {
                    $result = substr($string,0,$endPoint);
                }
                else
                {
                    $result = $string;
                }
                if($with)
                {
                    $result = $start.$result;
                    if($end)
                    {
                        $result.=$end;
                    }
                }
            }
        }
        return $result;
    }


}