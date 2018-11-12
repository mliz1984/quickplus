<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 7:39 PM
 */

namespace Quickplus\Lib\Tools;


class ArrayTools{
    public static function getValues($array,$col,$spilt=",",$marks="'")
    {
        $result = "";
        foreach($array as $data)
        {
            $str = $data[$col];
            if($str!=null&&trim($str)!="")
            {
                $result.=$spilt.$marks.$str.$marks;
            }
        }
        return ltrim($result,$spilt);

    }

    public static function arrayRecursive($array, $function, $apply_to_keys_also = false,$jsonPre=false)
    {
        $result = array();
        foreach ($array as $key => $value) {
            $newkey  = $key;
            if ($apply_to_keys_also && is_string($key)) {
                {
                    $newkey  = $function($key);
                    if($jsonPre)
                    {
                        $newkey = str_replace('"', '\"', $newkey);
                    }

                }
                if (is_array($value)) {
                    $result[$newkey] = self::arrayRecursive($array[$key], $function, $apply_to_keys_also,$jsonPre);
                } else {
                    if($jsonPre)
                    {
                        $value = str_replace('"', '\"', $value);

                    }
                    $value = $function($value);
                    $result[$newkey] = $value;

                }

            }
        }
        return $result;
    }



    public static function jsonToArray($json,$function="urldecode",$apply_to_keys_also = true)
    {
        $json  = StringTools::conv($json);
        $array = json_decode($json);
        $array = self::arrayRecursive($array, $function, $apply_to_keys_also);
        return $array;
    }

    public static function arrayToJson($array,$function="urlencode",$apply_to_keys_also = true,$autoDecode=true)
    {

        $array = self::arrayRecursive($array, $function, $apply_to_keys_also,true);

        $json = json_encode($array);
        if($autoDecode)
        {
            $json = urldecode($json);
        }
        return $json;
    }




    public static function getWidth($array,$isKeyValueArray=true)
    {
        $width = 0;
        if($isKeyValueArray)
        {
            foreach($array as $key => $val)
            {
                if(is_array($val))
                {
                    $tmp += ArrayTools::getWidth($val,$isKeyValueArray);
                }
                if($tmp>$depth)
                {
                    $width += 1;
                }
            }
        }
        else
        {
            foreach($array as $val)
            {
                if(is_array($val))
                {
                    $tmp += ArrayTools::getWidth($val,$isKeyValueArray);
                }
                if($tmp>$depth)
                {
                    $width += 1;
                }
            }
        }
        return $width;
    }
    public static function getDepth($array,$isKeyValueArray=true,$count=0)
    {
        $depth = $count;
        if($isKeyValueArray)
        {
            foreach($array as $key => $val)
            {
                $tmp = $count + 1 ;
                if(is_array($val))
                {
                    $tmp = ArrayTools::getDepth($val,$isKeyValueArray,$tmp);
                }
                if($tmp>$depth)
                {
                    $depth = $tmp;
                }
            }
        }
        else
        {
            foreach($array as  $val)
            {
                $tmp = $count + 1 ;
                if(is_array($val))
                {
                    $tmp = ArrayTools::getDepth($val,$isKeyValueArray,$tmp);
                }
                if($tmp>$depth)
                {
                    $depth = $tmp;
                }
            }
        }
        return $depth;
    }

    public static function getMixMap($oriarray,$orikey,$addarray,$addkey,$mappingArray=null)
    {
        return ArrayTools::getMixArray($oriarray,$orikey,$addarray,$addkey,$mappingArray,true);
    }
    public static function getMixArray($oriarray,$orikey,$addarray,$addkey,$mappingArray=null,$isMixMap = false)
    {
        if($dstkey==null||trim($dstkey)!="")
        {
            $dstkey = $addkey;
        }
        $oriarray = ArrayTools::getKeyDataArray($oriarray,$orikey);
        $addarray = ArrayTools::getKeyDataArray($addarray,$addkey);
        $result = Array();
        foreach($oriarray as $key=>$data)
        {
            $orivalue = $data[$orikey];
            $addvalue = null;
            if(is_array($addarray[$key]))
            {
                $addvalue = $addarray[$key][$addkey];
                if($orivalue==$addvalue)
                {
                    if($mappingArray==null)
                    {
                        foreach($addarray[$key] as $tkey =>$tvalue)
                        {
                            if($tkey!=$addkey)
                            {
                                $data[$tkey] = $tvalue;
                            }

                        }
                    }
                    else
                    {
                        foreach($mappingArray as $oldname => $newname)
                        {
                            $tmp =   $addarray[$key][$oldname];
                            $data[$newname] = $tmp;
                        }
                    }
                }
            }
            if($isMixMap)
            {
                $result[$key] = $data;
            }
            else
            {
                $result[] = $data;
            }
        }
        return $result;
    }
    public static function getKeyValueMap($array,$key,$value,$strval=false)
    {
        $result = Array();
        foreach($array as $data)
        {

            $akey = $data[$key];
            $avalue = $data[$value];
            if($strval)
            {
                $akey = strval($akey);
                $avalue =  strval($avalue);
            }
            $now = $result[$akey];
            if(is_array($now))
            {
                $now[] = $avalue;
            }
            else
            {
                $now = array($avalue,);
            }

            $result[$akey] = $now;

        }
        return $result;
    }

    public static function getKeyValueArray($array,$key,$value,$strval=false)
    {
        $result = Array();
        foreach($array as $data)
        {
            $akey = $data[$key];
            $avalue = $data[$value];
            if($strval)
            {
                $akey = strval($akey);
                $avalue =  strval($avalue);
            }
            $result[$akey] = $avalue;
        }
        return $result;
    }
    public static function getKeyDataMap($array,$key,$strKey=false)
    {
        $result = Array();
        foreach($array as $data)
        {
            $akey = $data[$key];
            $now = $result[$akey];
            if(is_array($now))
            {
                $now[] = $data;
            }
            else
            {
                $now = array($data,);
            }
            if($strKey)
            {
                $akey = strval($akey);
            }
            $result[$akey] = $now;
        }
        return $result;
    }

    public static function getKeyDataArray($array,$key,$strKey=false,$case=2)
    {
        $result = Array();
        foreach($array as $data)
        {

            $akey = $data[$key];
            if($strKey)
            {
                $aKey = strval($akey);
            }
            $temp = $data;
            if($case==CASE_LOWER||$case==CASE_UPPER)
            {

                $temp = array_change_key_case($temp,$case);
            }
            $result[$akey] = $temp;

        }
        return $result;
    }

    public static function getCsvString($array,$oriCsv=false)
    {
        $result = "";
        foreach($array as $data)
        {
            $str = "";
            foreach($data as $key=>$value)
            {
                $value = str_replace("\"","\"\"",$value);
                if($oriCsv)
                {
                    $str.= ",".$value;
                }
                else
                {
                    $str.= ",\"".$value."\"";
                }
            }
            $result.= substr($str, 1)."\n";
        }
        return $result;
    }

}