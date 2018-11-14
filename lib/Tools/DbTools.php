<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 7:38 PM
 */

namespace Quickplus\Lib\Tools;


class DbTools{
    public static function checkSql($db,$sql)
    {
        $result = false;
        if(DbTools::getColnames($sql,true))
        {
            $checkSql = "SELECT * FROM (".$sql.") sql_check WHERE 1=2";
            $result = $db->execSql($checkSql);
        }
        return $result;
    }

    public static function getTableKeyFormCol($col)
    {

        $tmp = explode(".", $col);
        return $tmp[0];
    }

    public static function getColNameFormCol($col)
    {
        $tmp = explode(".", $col);
        return $tmp[count($tmp)-1];
    }

    public static function getCountSql($sql,$colname="",$col=null)
    {

        $sql = trim($sql);
        $sql = str_replace(PHP_EOL, ' ', $sql);
        $sign = "FROM";
        $inpart = 0;
        if($col==null||trim($col)=="")
        {
            $col = "*";
        }
        $add = false;
        $countsql = "SELECT count(".$col.") ".$colname." ";
        $arr = explode(" ",$sql);
        $countArr = count($arr);
        $inpart = 0;
        for($i=0;$i<$countArr;$i++) {
            $value = $arr[$i];
            $inpart = $inpart + count(explode("(",$value)) -  count(explode(")",$value));
            if($inpart == 0 && trim(strtoupper($value))=="DISTINCT")
            {

                $countsql = "SELECT count(".$col.") ".$colname." FROM (".$sql.") totalcountsql";
                break;
            }
            if(!$add)
            {
                if(trim(strtoupper($value))==$sign&&$inpart == 0)
                {
                    $add = true;
                }
            }
            if($add)
            {
                if($inpart == 0 && trim(strtoupper($value)) == "GROUP" && ($i+1)<$countArr && trim(strtoupper($arr[$i+1]))=="BY")
                {

                    $countsql = "SELECT count(".$col.") ".$colname." FROM (".$sql.") totalcountsql";
                    break;
                }
                else
                {
                    if($inpart == 0 && strtoupper(trim($value))=="ORDER"&&strtoupper(trim($arr[$i+1]))=="BY")
                    {
                        break;
                    }
                    $countsql.=" ".trim($value);
                }
            }

        }

        return $countsql;
    }


    public static function getOrderArray($sql)
    {
        $sql = trim($sql);
        $sql = str_replace(PHP_EOL, ' ', $sql);
        $tmp = explode(" ",$sql);
        $result = Array();
        $result["select"] = $sql;
        $orderArr["order"] = null;
        $start = null;
        $arr = Array();
        foreach($tmp as $t)
        {
            if($t!=null&&trim($t)!="")
            {
                $arr[] = $t;
            }
        }
        $length = count($arr);
        for($i=0;$i<$length;$i++)
        {
            if($i+1<$length&&strtoupper(trim($arr[$i]))=="ORDER"&&strtoupper(trim($arr[$i+1]))=="BY"&&($i>$start||$start==null))
            {
                $start = $i;
            }
        }
        if($start!=null)
        {
            $order = array_slice($arr,$start+2);
            $tmp = "";
            foreach($order as $a)
            {
                $tmp.=" ".$a;
            }
            $tmp = trim($tmp);
            $tmp=explode(",",$tmp);

            if(count($tmp)>0)
            {

                $result = Array();
                $select =  implode(" ",array_slice($arr,0,$start));
                $result["select"] = $select;
                $orderArr["order"] = Array();
                foreach($tmp as $c)
                {
                    $ttmp = explode(" ",$c);
                    $t = Array();
                    foreach($ttmp as $ttt)
                    {
                        if($ttt!=null&&trim($ttt)!="")
                        {
                            $t[] = $ttt;
                        }
                    }

                    if(count($t)>0)
                    {
                        $a =  Array();
                        $tt = explode(".", $t[0]);
                        $a["col"] = $tt[count($tt)-1];
                        $a["fullcol"] = $t[0];
                        $type = "ASC";
                        if(count($t)>1)
                        {
                            for($j=1;$j<count($t);$j++)
                            {
                                if(strtoupper(trim($t[$j]))=="DESC")
                                {
                                    $type = "DESC";
                                }
                            }
                        }
                        $a["type"] = $type;
                        $result['order'][] = $a;
                    }
                }
            }
        }
        return $result;
    }

    public static function getColNames($sql,$check=false)
    {
        $checkResult = true;
        $voidArray = Array("DISTINCT",);
        $sql = trim($sql);
        $sql = str_replace(PHP_EOL, ' ', $sql);
        $startSign = "SELECT";
        $stopSign = "FROM";
        $arr = explode(" ",$sql);
        $tmp = "";
        $add = false;
        $inpart = 0;
        foreach ($arr as $value) {

            $inpart = $inpart + count(explode("(",$value)) -  count(explode(")",$value));
            if(trim(strtoupper($value))==$stopSign&&$inpart == 0)
            {
                break;
            }
            if($add)
            {
                $value = trim($value);
                if(!in_array($value, $voidArray))
                {
                    $tmp.= " ".$value;
                }
            }
            if(trim(strtoupper($value))==$startSign)
            {
                $add = true;

            }

        }
        $temp = explode(",",$tmp);

        $arr = Array();
        $inpart = 0;
        $string = "";
        foreach($temp as $t)
        {
            if($string!="")
            {
                $string.=",";
            }
            $string .= $t;
            $inpart = $inpart + count(explode("(",$t)) -  count(explode(")",$t));
            if($inpart == 0)
            {
                $arr[] = $string;
                $string = "";
            }

        }

        $result = Array();
        foreach ($arr as $value) {
            $value = trim($value);
            if($value!=null&&$value!="")
            {
                $k ="";
                $v ="";
                $tmp = explode(" ",$value);
                if(count($tmp)>1)
                {
                    for($i=0;$i<count($tmp)-1;$i++)
                    {
                        $t = $tmp[$i];
                        if($v!="")
                        {
                            $v .= " ";
                        }
                        if(trim(strtoupper($t))=="AS")
                        {
                            break;
                        }
                        $v.=$t;
                    }
                }
                else
                {
                    $v = $tmp[0];
                }

                if($tmp!=null&&trim($tmp[count($tmp)-1])!="")
                {
                    $tmp =  trim($tmp[count($tmp)-1]);
                    $tmp = explode(".",$tmp);
                    if($tmp!=null&&trim($tmp[count($tmp)-1])!="")
                    {
                        $k = trim($tmp[count($tmp)-1]);
                    }
                }
                if(strtoupper(trim($k))!="TOP")
                {
                    if($check&&isset($result[str_replace("`", "", $k)]))
                    {
                        $checkResult = false;
                    }
                    $k = str_replace("`", "", $k);
                    $k = str_replace("[", "", $k);
                    $k = str_replace("]", "", $k);
                    $v = str_replace("`", "", $v);
                    $result[$k] = $v;
                }

            }
        }
        if($check)
        {
            $result = $checkResult;
        }
        return $result;
    }

    public static function getIdStrFromString($string,$sign=",")
    {
        $array = explode($sign, $string);
        return DbTools::getIdStrFromArray($array);
    }

    public static function getIdStrFromArray($array,$spilt=",",$marks="'")
    {
        $result = "";
        foreach($array as $str)
        {
            if($str!=null&&trim($str)!="")
            {
                $result.=$spilt.$marks.$str.$marks;
            }
        }
        return ltrim($result,$spilt);
    }
}