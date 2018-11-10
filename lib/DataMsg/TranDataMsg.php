<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 7:28 PM
 */

namespace Lib\DataMsg;
use Lib\DataMsg\DataMsg as DataMsg;

class TranDataMsg extends DataMsg{
    protected $tranList =Array();
    public function getTranList()
    {
        return $this->tranList();
    }
    public function clearTranList()
    {
        $this->tranList = Array();
    }
    public function addTran($prefix="tr_",$prefixMethodName="",$isExoprt=false)
    {

        $temp =Array(
            "prefix" => $prefix,
            "prefixmethod" => $prefixMethodName,
            "isexoprt" => $isExoprt,

        );
        $this->tranList = array_merge($this->tranList,array($temp,));
        $dataMsg = $this->getTranDataMsg($prefix,$prefixMethodName,$isExoprt);
        $this->setDataMsg($dataMsg);
    }


    public function getTranData($i,$prefix="tr_",$prefixMethodName="",$isExport=false)
    {
        $data = $this->getData($i);
        foreach($data->getDataArray() as $key=>$value)
        {
            $methodName = $prefixMethodName.$key;
            $newkey =  $prefix.$key;
            $newvalue = $value;

            if(method_exists($this,$methodName))
            {
                $newvalue = $this->getValue($i,$key,true,$isExport,true,$methodName);
            }
            $data->set($newkey,$newvalue);
        }
        return $data;
    }

    public  function getTranDataMsg($prefix="tr_",$prefixMethodName="",$isExport=false)
    {
        return  $this->dataMsgTran($this,$prefix,$prefixMethodName,$isExport);
    }

    public function dataMsgTran($dataMsg,$prefix="tr_",$prefixMethodName="",$isExport=false)
    {
        $result = $dataMsg;
        for($i=0;$i<$this->getResultSize();$i++)
        {
            $data = $this->dataTran($i,$prefix,$prefixMethodName,$isExport);
            $result->setData($i,$data);
        }
        return $result;
    }
    public function setDataMsg($datamsg)
    {
        $this->result = $datamsg->getResult();
    }
    public function getTran($i, $name,$prefix="tr_")
    {
        $name = $prefix.$name;
        return $this->get($i, $name);
    }



}