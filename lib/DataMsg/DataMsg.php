<?php
namespace Quickplus\Lib\DataMsg;
use Quickplus\Lib\DataMsg\BaseVo as BaseVo;
use Quickplus\Lib\DataMsg\Data as Data;
use Quickplus\Lib\QuickCrypt as QuickCrypt;
use Quickplus\Lib\Tools\DbTools as DbTools;
use Quickplus\Lib\QuickFromConfig;

class DataMsg
{
    protected $result = array();
    protected $db;
    protected $totalcount;
    protected $pagerows;
    protected $curpage;
    protected $totalpages;
    protected $pdoMode = false;
    protected $csvTitle = null;
    protected $csvDbTitle = null;
    protected $protectStatus = true;
    protected $srvSqlMainCol = "id";

    public function setSrvSqlMainCol($srvSqlMainCol)
    {
        $this->srvSqlMainCol = $srvSqlMainCol;
    }

    public function getSrvSqlMainCol()
    {
        return  $this->srvSqlMainCol;
    }

    public function openProtectStatus()
    {
        $this->protectStatus = true;
    }
    public function closeProtectStatus()
    {
        $this->protectStatus = false;
    }
    public function getProtectStatus()
    {
        return $this->protectStatus;
    }
    public function addCsvTitle($title)
    {
        $this->csvTitle[] = $title;
    }
    public function addCsvDbTitle($title)
    {
        $this->csvDbTitle[] = $title;
    }
    public function setCsvTitle($csvTitle)
    {
        $this->csvTitle = $csvTitle;
    }

    public function setCsvDbTitle($csvDbTitle)
    {
        $this->csvDbTitle = $csvDbTitle;
    }

    public function getCsvTitle()
    {
        return $this->csvTitle;
    }

    public function getCsvDbTitle()
    {
        return $this->csvDbTitle;
    }

    public function setCsvArray($csvArray,$db,$tablename="",$primaryKey="")
    {
        for($i=0;$i<count($csvArray);$i++)
        {
            $dataArray =  $csvArray[$i];
            $data = new Data($db,$tablename,$primaryKey);
            $data->setDataArray($dataArray);
            $this->addData($data);
        }
        return $this;
    }
    public function loadFromCsvFile($tablename,$idcol,$fileName,$loadTitle=false,$start=1,$end=null)
    {
        $db = $this->db;
        $tmp = $this->getCsvArrayByFile($fileName,$start,$end,$loadTitle,$loadTitle,true,true);
        foreach($tmp as $t)
        {
            $data =new Data($db,$tablename,$idcol);
            $data->setDataArray($t);
            $this->addData($data);
        }

    }

    public function getCsvArrayByFile($file,$start=1,$end=null,$haveTitle=true,$loadTitle=true,$withname=true,$dbname=true)
    {
        return $this->getCsvArrayByString(file_get_contents($file),$start,$end,$haveTitle,$loadTitle,$withname,$dbname);
    }

    public function getCsvArrayByString($string,$start=1,$end=null,$haveTitle=true,$loadTitle=true,$withname=true,$dbname=true)
    {

        $string = str_replace("\r\n","\n",$string);
        $string = str_replace("\r","\n",$string);
        $array = explode("\n", $string);

        $result = Array();
        $count = count($array);
        $itemCount = $count;
        if($haveTitle)
        {
            $itemCount = $itemCount - 1;
        }
        if($itemCount<$end||$end==null)
        {
            $end = $itemCount;
        }
        if($haveTitle)
        {
            $start = $start +1;
            $end =  $end  +1;
        }

        if($count>=$start)
        {

            if($haveTitle&&$loadTitle)
            {

                if($dbname)
                {
                    $this->csvDbTitle =$array[0];
                }
                else
                {
                    $this->csvTitle = $array[0];
                }
            }
            $start = $start -1;
            $end  = $end -1;
            for($i=$start;$i<=$end;$i++)
            {

                $data = new Data($db);
                if($dbname)
                {

                    $data->setCsvDbTitle($this->csvDbTitle);
                }
                else
                {
                    $data->setCsvTitle($this->csvTitle);
                }
                $result[] = $data->getCsvArrayByString($array[$i],$withname,$dbname,true);
            }
        }
        return $result;
    }



    public function setPdoMode($pdoMode=true)
    {
        $this->pdoMode = $pdoMode;
    }
    public function getTotalCount()
    {
        return $this->totalcount;
    }

    public function getPageRows()
    {
        return $this->pagerows;
    }

    public function getTotalPages()
    {
        return $this->totalpages;
    }
    public function getCurPage()
    {
        return $this->curpage;
    }
    function __construct($db=null)
    {
        if($db!=null)
        {

            $this->setDb($db);
        }
    }

    public function setDb($db)
    {

        $this->db = $db;
    }
    public function getResultSize()
    {
        return count($this->result);
    }
    public function getData($i,$getArray=false)
    {
        $result = $this->result[$i];
        if($getArray)
        {
            $result = $result->getDataArray();
        }
        return $result;
    }
    public function addDataMsg($dataMsg)
    {
        $this->result = array_merge($this->result,$dataMsg->getResult());
    }
    public function setData($i,$data)
    {
        $this->result[$i] = $data;
    }
    public function getResult()
    {
        return $this->result;
    }
    public function getResultArray()
    {
        $result = Array();
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $result[] = $data->getDataArray();
        }
        return $result;
    }
    public function setDataArray($db,$tablename,$idCol,$dataArray)
    {
        foreach($dataArray as $d)
        {
            $data = new Data($db,$tablename,$idCol);
            $data->setDataArray($d);
            $this->addData($data);
        }
    }
    public function getDataArray()
    {
        return $this->getResultArray();
    }

    public function get($i,$name,$defaultValue="")
    {
        $data = $this->getData($i);
        return $data->get($name,$defaultValue);
    }
    public function getValue($i,$name,$isTableItem=true,$isExoprt=false,$isMethod=false,$methodName=null)
    {
        $data = $this->getData($i);
        if($isTableItem&&$isMethod)
        {
            if($methodName==null)
            {
                $method = $name;
            }
            return $this->$methodName($i,$name,$isExoprt);
        }
        return $data->get($name);

    }
    public function getValueByDbName($i,$name,$isTableItem=true,$isExoprt=false,$isMethod=false,$methodName="")
    {

        return $this->getValue($i,$name,$isTableItem,$isExoprt,$isMethod,$methodName);
    }

    public function addData($data)
    {
        $this->result[] = $data;
    }

    public function getUniData($db,$sql,$isArray=false)
    {
        $datamsg = new DataMsg($db);
        $datamsg->findBySql($db,$sql);
        if($datamsg->getSize()>0)
        {

            $result =  $datamsg->getData(0);
            if($isArray)
            {
                $result = $result->getDataArray();
            }

        }
        else
        {

            $result = null;
        }

        return $result;

    }

    public function getUniInt($db,$sql,$colname,$defaultValue=0)
    {
        $data = $this->getUniData($db,$sql);

        $result = $defaultValue;
        if($data!=null)
        {
            $result = $data->getInt($colname,$defaultValue);
        }
        return $result;
    }

    public function getUniFloat($db,$sql,$colname,$defaultValue=0.00,$precision=null)
    {
        $data = $this->getUniData($db,$sql);
        $result = $defaultValue;
        if($data!=null)
        {
            $result = $data->getFloat($colname,$defaultValue,$precision);
        }
        return $result;
    }

    public function getUniString($db,$sql,$colname,$defaultValue="")
    {
        $data = $this->getUniData($db,$sql);
        $result = $defaultValue;
        if($data!=null)
        {
            $result = $data->getString($colname,$defaultValue);
        }
        return $result;
    }

    protected function getCate($key,$isArray=false)
    {
        $result = Array();
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $value = $data->get($key);
            if($isArray)
            {
                if($isArray[$value] ==null)
                {
                    $isArray[$value] = Array();
                }
                $isArray[$value][] =  $data->getDataArray();
            }
            else
            {
                if($isArray[$value] ==null)
                {
                    $isArray[$value] = new DataMsg();
                }
                $isArray[$value]->addData($data);
            }
        }
        return $result;
    }

    public function getCateArray($key)
    {
        return $this->getCate($key,true);
    }

    public function getCateDataMag($key)
    {
        return $this->getCate($key);
    }
    public function toArray()
    {
        $result = Array();
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $result[] =  $data->getDataArray();
        }
        return $result;
    }



    public function getIdStrs($key,$splitBy=",",$mark="'")
    {
        $result = null;
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $id = $data->get($key);

            if($result==null)
            {
                $result = $mark.$id.$mark;
            }
            else {
                $result .= $splitBy.$mark.$id.$mark;
            }
        }
        return $result;
    }

    public function getCountValue($db,$sql,$countcol=null)
    {

        $countSql = DbTools::getCountSql($sql,"totalcount",$countcol);

        $totalcount  = $this->getUniInt($db,$countSql,"totalcount");
        //+echo $countSql;
        return $totalcount;
    }

    public function getPageSql($sql,$pagerows=0,$curpage=1)
    {
        if($pagerows>0)
        {
            $offset = ($curpage-1) * $pagerows;
            $limitSql = " LIMIT ".$offset.",".$pagerows;
            $sql = "SELECT * FROM ( ".$sql." ) pages ".$limitSql;
        }
        return $sql;
    }

    public function getSrvSqlPageSql($sql,$pagerows=0,$curpage=1,$srvSqlMainCol="id")
    {

        if($pagerows!=0)
        {
            $tmpOrderArr = DbTools::getOrderArray($sql);
            $colArr = DbTools::getColNames($sql);
            $orderArr = Array();

            $orderArr["select"] = $tmpOrderArr["select"];
            foreach($tmpOrderArr["order"] as $order)
            {
                $fullCol = $order["fullcol"];

                foreach($colArr as $k =>$v)
                {
                    if(trim($v)==trim($fullCol))
                    {
                        $order["pageordercol"] = trim($k);

                    }

                }
                $orderArr["order"][] = $order;
            }

            $sql = "SELECT TOP ".$pagerows." datamsg_pagea.* FROM ( ".$orderArr["select"]." ) datamsg_pagea ";
            if($curpage==1)
            {


                if($orderArr["order"]!=null&&is_array($orderArr["order"])&&count($orderArr["order"])>0)
                {
                    $sql.= " ORDER BY ";
                    $orderClause = "";
                    foreach($orderArr["order"] as $order)
                    {
                        $orderClause.= ",datamsg_pagea.".$order["pageordercol"]." ".$order["type"];
                    }
                    $orderClause = ltrim($orderClause,",");
                    $sql .= $orderClause;
                }
            }
            else
            {
                $select = $orderArr["select"];
                $offset = ($curpage-1) * $pagerows;
                $sql .= " WHERE datamsg_pagea.".$srvSqlMainCol." NOT IN ( SELECT TOP ".$offset." datamsg_pageb.".$srvSqlMainCol." FROM ( ".$select .") datamsg_pageb  ";
                if($orderArr["order"]!=null&&is_array($orderArr["order"])&&count($orderArr["order"])>0)
                {
                    $sql.= " ORDER BY ";
                    $orderClause = "";
                    foreach($orderArr["order"] as $order)
                    {
                        $orderClause.= ",datamsg_pageb.".$order["pageordercol"]." ".$order["type"];
                    }
                    $orderClause = ltrim($orderClause,",");
                    $sql .= $orderClause;
                }
                $sql .= ") ";
                if($orderArr["order"]!=null&&is_array($orderArr["order"])&&count($orderArr["order"])>0)
                {
                    $sql.= " ORDER BY ";
                    $orderClause = "";
                    foreach($orderArr["order"] as $order)
                    {
                        $orderClause.= ",datamsg_pagea.".$order["pageordercol"]." ".$order["type"];
                    }
                    $orderClause = ltrim($orderClause,",");
                    $sql .= $orderClause;
                }

            }

        }
        return $sql;
    }


    public function getPages($totalcount,$pagerows=0)
    {
        if($pagerows==0)
        {
            $totalpage = 1;
        }
        else {
            $totalpage = intval($totalcount/$pagerows);
            $temp = $totalcount%$pagerows;
            if($temp>0)
            {
                $totalpage = $totalpage+1;
            }
        }
        return $totalpage;
    }

    public function findBySqlBuilder($db,$sqlBuilder,$dataArray=null,$pagerows=0,$curpage=1,$countcol=null,$tableName="")
    {
        $sql = $sqlBuilder->getSql($dataArray);
        return $this->findByPageSql($db,$sql,$pagerows,$curpage,$countcol,$tableName,$sqlBuilder);
    }



    public function findByPageSql($db,$sql,$pagerows=0,$curpage=1,$countcol=null,$tableName="",$sqlBuilder=null,$getSql=false,$orderBy=null)
    {
        if($orderBy!=null&&trim($orderBy)!="")
        {
            $sql = $sql." ".$orderBy;
        }
        if($pagerows!=0)
        {
            if(!$getSql)
            {
                $this->pagerows = $pagerows;
                $this->curpage = $curpage;
                $totalcount  = $this->getCountValue($db,$sql,$countcol);
                $this->totalcount = $totalcount;
                $totalpage = $this->getPages($totalcount,$pagerows);
                $this->totalpages = $totalpage;
            }

            if($db->isMsSql())
            {
                $sql = $this->getSrvSqlPageSql($sql,$pagerows,$curpage,$this->srvSqlMainCol);

            }
            else
            {
                $sql = $this->getPageSql($sql,$pagerows,$curpage);
            }

        }
        else {
            $this->pagerows = 0;
        }
        if($getSql)
        {
            return $sql;
        }

        return $this->findBySql($db,$sql,$tableName,false,$sqlBuilder);
    }

    public function findByMultiSql($db,$sqlArray,$getArray=false)
    {
        return $this->findByMutilSql($db,$sqlArray,$getArray);
    }

    public function findByMutilSql($db,$sqlArray,$getArray=false)
    {
        $result = Array();
        $mutilSql = "";
        $tableArray = Array();
        foreach($sqlArray as $tablename => $sql)
        {
            if($mutilSql!="")
            {
                $mutilSql .=";";
            }
            $mutilSql .=$sql;
            $tableArray[] = $tablename;
        }
        $pdo = $db->getPdo();
        $stmt = $pdo->query($mutilSql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = Array();
        $i = -1;
        do {
            $i++;
            $tableName = $tableArray[$i];

            if($getArray)
            {
                $dataset = $stmt->fetchAll();

                $result[$tableName] = $dataset;
            }
            else
            {
                $dataMsg = new DataMsg($db);
                foreach($dataset as $dataArray)
                {
                    $data = new data($db);
                    $data->setDataArray($dataArray);
                    $data->setSql($sqlArray[$i]);
                    $data->setTableName($tableName);
                    $dataMsg->addData($data);
                }
                $result[$tableName] = $dataMsg;


            }

        } while($stmt->nextRowset());
        return $result;
    }


    public function findByLiteSql($db,$sql,$tableName="",$getArray=false,$sqlBuilder=null)
    {
        $result = Array();
        $tmp = $db->query($sql);


        while ($row_data = $tmp->fetchArray(SQLITE3_ASSOC))
        {

            if($getArray)
            {
                $result[] = $row_data;

            }
            else
            {
                $data = new Data($db);
                $data->setDataArray($row_data);
                $data->setTableName($tableName);
                $data->setSql($sql);
                if($sqlBuilder!=null)
                {
                    $data->setSqlBuilder($sqlBuilder);
                }
                $result[] = $data;
            }
        }
        if($getArray)
        {
            return $result;
        }

        $this->result = $result;
        return $this;
    }


    public function findBySql($db,$sql,$tableName="",$getArray=false,$sqlBuilder=null)
    {
        $result = Array();
        if($db instanceof sqlLite)
        {
            return $this->findByLiteSql($db,$sql,$tableName,$getArray,$sqlBuilder);
        }
        $db->openQuery($sql);

        if($db->result){

            foreach($db->result as $r)
            {
                if($getArray)
                {
                    $result[] = $r;
                }
                else
                {
                    $data = new Data($db);
                    $data->setDataArray($r);
                    $data->setTableName($tableName);
                    $data->setSql($sql);
                    if($sqlBuilder!=null)
                    {
                        $data->setSqlBuilder($sqlBuilder);
                    }
                    $result[] = $data;
                }
            }
            /*  $func = "mssql_fetch_array";
               $para = MSSQL_ASSOC;
               if($db->isSqlSrv())
               {
                             $func = "sqlsrv_fetch_array";
                             $para = SQLSRV_FETCH_ASSOC;

               }
               while($row_data=$func($db->result, $para)){

                    if($getArray)
                    {
                       $result[] = $row_data;
                    }
                    else
                    {
                       $data = new Data($db);
                       $data->setDataArray($row_data);
                       $data->setTableName($tableName);
                       $data->setSql($sql);
                       if($sqlBuilder!=null)
                       {
                         $data->setSqlBuilder($sqlBuilder);
                       }
                       $result[] = $data;
                    }
                 }
           }
           else {

                while($row_data=mysql_fetch_assoc($db->result)){
                    if($getArray)
                    {
                       $result[] = $row_data;
                    }
                    else
                    {
                      $data = new Data($db);
                      $data->setDataArray($row_data);
                      $data->setTableName($tableName);
                      $data->setSql($sql);
                      if($sqlBuilder!=null)
                       {
                         $data->setSqlBuilder($sqlBuilder);
                       }
                      $result[] = $data;
                    }
                 }
           }*/

        }


        if($getArray)
        {
            return $result;
        }
        $this->result = $result;
        return $this;

    }
    public function getSize()
    {
        return $this->getResultSize();
    }


    public function getJsonString($autoDecode=true)
    {
        $temp = "";
        for($i=0;$i<$this->getResultSize();$i++)
        {
            $data = $this->getData($i);
            $temp.=",".$data->getJsonString($autoDecode);
        }
        $temp = substr($temp,1);
        $result =  "{\"totalResultsCount\":".$this->getResultSize().",\"datas\":[".$temp."]}";
        return $result;
    }

    public function execSql($db,$sql)
    {
        return $db->execSql($sql);
    }



    public function findByData($data,$pagerows=0,$curpage=1,$getSql=false)
    {
        if($data->isVt())
        {
            return $this->findByVData($data,$pagerows,$curpage,$getSql);
        }
        $db = $data->getDb();
        $sql = null;
        $countcol = null;
        $sql = null;
        $sqlBuilder = null;
        if($data->getSqlBuilder()!=null)
        {
            $sqlBuilder = $data->getSqlBuilder();
            $sql = $sqlBuilder->getSql($data->getDataArray());
        }
        else
        {
            $sql = "SELECT * FROM ".$data->getTableName();
            if($data->haveSubTable())
            {
                $sql = $data->getUnitedSql();
            }
            $where = $data->getWhere();
            $hasWhere = false;
            if($where!=null)
            {
                $whereSql = $where->getWhereSql($data->getColinfo(),$data->getDataArray());
                if($whereSql!=null&&trim($whereSql)!="")
                {
                    $sql .= " WHERE ".$whereSql;
                    $hasWhere = true;
                }
            }
            if(!$hasWhere&&$data->getWhereClause()!=null)
            {
                $sql .= " WHERE ".$data->getWhereClause();
            }
            if($data->getOrderClause()!=null)
            {
                $sql.=" ORDER BY ".$data->getOrderClause();
            }

            if($data->primaryKey!=null&&$data->primaryKey[0]!=null)
            {
                $countcol = $data->primaryKey[0];
            }
        }

        return $this->findByPageSql($db,$sql,$pagerows,$curpage,$countcol,$data->getTableName(),$sqlBuilder,$getSql);
    }

    protected function findByVData($data,$pagerows=0,$curpage=1,$getSql=false)
    {

        $result = null;
        if(!$data->isVt())
        {
            $result = $this->findByData($data,$pagerows,$curpage,$getSql);
        }
        else
        {

            $sql = $data->getVtMainIdSql();
            $idMsg = new DataMsg();
            $idMsg =$idMsg->findByPageSql($data->getDb(),$sql,$pagerows,$curpage);


            $result = new DataMsg();
            if($idMsg->getSize()>0)
            {
                $idStr = $idMsg->getIdStrs($data->getVtIdCol());
                $findData = new Data($data->getDb(),$data->getTableName(),$data->primaryKey[0]);
                $findData->setTableSign($data->getTableSign());
                $findData->setSubTables($data->getSubTables());
                $findData->setWithOperator($data->getVtIdCol(),"(".$idStr.")","IN");

                $fdMsg = $findData->find();
                $tmp = Array();
                $tprimaryKey = $data->getPrimaryKey();
                for($i=0;$i<$fdMsg->getSize();$i++)
                {
                    $fd = $fdMsg->getData($i);
                    $id = $fd->get($data->getVtIdCol());
                    if(isset($tmp[$id])&&$tmp[$id]!=null)
                    {
                        $td = $tmp[$id];
                    }
                    else
                    {
                        $td = new Data($data->getDb(),$data->getTableName(),$tprimaryKey[0]);
                        $td->setVtInfo($data->getVtIdCol(),$data->getVtKeyCol(),$data->getVtValueCol());
                    }
                    $fdArray = $fd->getDataArray();
                    $vkey  = null;
                    $vvalue = null;
                    foreach($fdArray as $fdkey=>$fdvalue)
                    {

                        if($fdkey == $data->getVtKeyCol())
                        {

                            $vkey = $fdvalue;
                        }
                        else if($fdkey==$data->getVtValueCol())
                        {

                            $vvalue = $fdvalue;
                        }
                        else if($fdkey!=$tprimaryKey[0])
                        {
                            $td->set($fdkey,$fdvalue);
                        }
                    }

                    $td->set($vkey,$vvalue);

                    $tmp[$id] = $td;
                }
                foreach($tmp as $cvid=>$cvdata)
                {
                    $result->addData($cvdata);
                }

            }
        }
        return $result;
    }

    public function batchUpdate($sqlMode=false)
    {
        $db = $this->db;
        $sql = Array();
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            if($this->getProtectStatus())
            {
                $data->openProtectStatus();
            }
            else
            {
                $data->closeProtectStatus();
            }
            $data->setDb($db);
            $sql[]=$data->update(true);
        }
        if(count($sql)>0)
        {
            if($sqlMode)
            {
                return $sql;
            }
            return $db->execTransaction($sql);
        }
    }
    public function isExistTable($db,$tablename)
    {
        $result = false;
        $queryFunc = "mysql_query";
        $numFunc = "mysql_num_rows";
        if($db->isMsSql())
        {
            $queryFunc = "mssql_query";
            $numFunc = "mssql_num_rows";
            if($db->isSqlSrv())
            {
                $queryFunc = "sqlsrv_query";
                $numFunc = "sqlsrv_num_rows";
            }
        }

        if($numFunc($queryFunc("SHOW TABLES LIKE '%" . $tablename . "%'")==1)) {
            $result = true;
        }
        return $result;
    }

    public function copyTable($db,$src,$dst,$withData=true,$withId=false)
    {
        $createSql = "CREATE TABLE IF NOT EXISTS $dst LIKE  $src ";
        $this->execSql($db,$createSql);
        if($withData)
        {
            $copySql = "INSERT INTO $dst SELECT * FROM $src ";
            if(!$withId)
            {
                $temp = " SHOW FULL COLUMNS FROM $src WHERE ".$db->processTableObj("Key")." = '' ";
                $dataMsg =new DataMsg();
                $dataMsg->findBySql($db,$temp);
                $fileds = $dataMsg->getIdStrs("Field",",","");
                $copySql = "INSERT INTO $dst ($fileds) SELECT $fileds FROM $src";
                $this->execSql($db,$copySql);
            }
            $this->execSql($db,$copySql);
        }
    }

    public function batchExec($db,$array)
    {
        $result = false;
        if(is_array($array)&&count($array)>0)
        {
            $result =  $db->execTransaction($array);
        }
        return $result;
    }



    public function batchCreate($sqlMode=false)
    {
        if($this->pdoMode)
        {
            return  $this->pdoBatchCreate();
        }

        $db = $this->db;
        $sql = Array();
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $data ->setDb($db);
            $tmp = $data->create(true);
            if(is_array($tmp))
            {
                $sql = array_merge($sql,$tmp);
            }
            else
            {
                $sql[] = $tmp;
            }
        }
        if(count($sql)>0)
        {
            if($sqlMode)
            {
                return $sql;
            }
            return $db->execTransaction($sql);
        }

    }

    public function voidEmpty($voidSpace=false)
    {
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $data ->voidEmpty($voidSpace);
            $this->setData($i,$data);
        }
    }

    public function remove($key)
    {
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $data ->remove($key);
            $this->setData($i,$data);
        }
    }

    public function set($key,$value)
    {
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $data ->set($key,$value);
            $this->setData($i,$data);
        }
    }

    public function addPrimaryKey($key)
    {
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $data ->addPrimaryKey($tablename);
            $this->setData($i,$data);
        }
    }

    public function setTableName($tablename)
    {
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $data ->setTableName($tablename);
            $this->setData($i,$data);
        }
    }



    protected function pdoBatchCreate()
    {

        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $data ->setDb($this->db);
            $data ->setPdoMode($this->pdoMode);
            $data->Create();
        }
        return 0;
    }

    public function batchDelete($sqlMode=false)
    {
        if($this->pdoMode)
        {
            return  $this->pdoBatchDelete();
        }
        $db = $this->db;
        $sql = Array();

        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $data ->setDb($db);
            if($this->getProtectStatus())
            {
                $data->openProtectStatus();
            }
            else
            {
                $data->closeProtectStatus();
            }
            $sql[]=$data->delete(true);
        }
        if(count($sql)>0)
        {
            if($sqlMode)
            {
                return $sql;
            }
            return $db->execTransaction($sql);
        }
    }

    public function batchDeleteByPrimaryKey($sqlMode=false)
    {

        $db = $this->db;
        $sql = "";

        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            if($this->getProtectStatus())
            {
                $data->openProtectStatus();
            }
            else
            {
                $data->closeProtectStatus();
            }
            $data->setDb($db);
            $sql.=";".$data->deleteByPrimaryKey(true);
        }
        if(strlen($sql)>0)
        {
            $sql = substr($sql,1);
            $sql = explode(";",$sql);
            if($sqlMode)
            {
                return $sql;
            }
            return $db->execTransaction($sql);
        }
    }

    protected function pdoBatchDelete()
    {

        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $data ->setDb($this->db);
            $data ->setPdoMode($this->pdoMode);
            if($this->getProtectStatus())
            {
                $data->openProtectStatus();
            }
            else
            {
                $data->closeProtectStatus();
            }
            $data->Delete();
        }
        return 0;
    }
    public function batchCreateUpdate($sqlMode=false)
    {
        if($this->pdoMode)
        {
            return  $this->pdoBatchCreateUpdate();
        }
        $db = $this->db;
        $sql = Array();
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $data->setDb($db);
            if($this->getProtectStatus())
            {
                $data->openProtectStatus();
            }
            else
            {
                $data->closeProtectStatus();
            }
            $sql[] = $data->createUpdate(true);
        }
        if(count($sql)>0)
        {

            if($sqlMode)
            {
                return $sql;
            }
            return $db->execTransaction($sql);
        }
    }

    protected function pdoBatchCreateUpdate()
    {

        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $data ->setDb($this->db);
            $data ->setPdoMode($this->pdoMode);
            if($this->getProtectStatus())
            {
                $data->openProtectStatus();
            }
            else
            {
                $data->closeProtectStatus();
            }
            $data->createUpdate();
        }
        return 0;
    }
    public function toXml()
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<datas>";
        $size = $this->getSize();
        for($i=0;$i<$size;$i++)
        {
            $data = $this->getData($i);
            $xml.=$data->toXml();
        }
        $xml.="</datas>";
        return $xml;
    }

    public function loadXml($xmlStr)
    {
        $xml = new SimpleXMLElement($xmlStr);
        foreach($xml->datas->data as $item)
        {
            $data = new Data();
            $attr = $item->attributes();
            $data->setTableName($attr["tableName"]);
            foreach($item->children() as $row)
            {
                $rowattr = $row->attributes();
                if($rowattr["pk"]=="true")
                {
                    $data->addPrimaryKey($row->getName());
                    $data->set($row->getName(), $row);
                }
            }
            $this->addData($data);
        }
        return $this;

    }

    public function getKeyValueMap($key,$value,$strval=false)
    {
        $result = Array();
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $akey = $data->get($key);
            $avalue = $data->get($value);
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

    public function getValueList($key,$strvel=false)
    {
        $result = Array();
        if($strval)
        {
            $key =strval($key);
        }
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            $value = $data->get($key);
            if($strval)
            {
                $value =strval($value);
            }
            $result[] = $value;
        }
        return $result;
    }

    public function getKeyValueArray($key,$value,$strval=false)
    {
        $result = Array();
        if($strval)
        {
            $key =strval($key);
        }
        for($i=0;$i<$this->getSize();$i++)
        {

            $data = $this->getData($i);
            $akey = $data->get($key);

            $avalue = $data->get($value);
            if($strval)
            {
                $akey = strval($akey);
                $avalue =  strval($avalue);
            }
            $result[$akey] = $avalue;
        }
        return $result;
    }

    public function clear()
    {
        $this->result =  array();
    }

    public function getKeyValueArrayBySql($db,$sql,$key,$value)
    {
        $this->findBySql($db,$sql);
        return $this->getKeyValueArray($key,$value);
    }
    public function getKeyDataMap($key,$isArray=false,$strKey=false)
    {
        $result = Array();
        if($strval)
        {
            $key =strval($key);
        }
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);

            $akey = $data->get($key);
            if($isArray)
            {
                $data = $data->getDataArray();
            }
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
    public function getKeyDataArray($key,$isArray=false,$strKey=false,$case=false)
    {

        $result = Array();
        for($i=0;$i<$this->getSize();$i++)
        {

            $data = $this->getData($i);
            $akey = $data->get($key);
            if($strKey)
            {
                $aKey = strval($akey);
            }
            if($isArray)
            {

                $temp = $data->getDataArray();

                if($case!==false&&$case==CASE_LOWER||$case==CASE_UPPER)
                {

                    $temp = array_change_key_case($temp,$case);
                }
                $result[$akey] = $temp;
            }
            else
            {
                $result[$akey] = $data;
            }
        }
        return $result;
    }
    public function merge($dataMsg)
    {
        for($i=0;$i<$dataMsg->getSize();$i++)
        {
            $data = $dataMsg->getData($i);
            $this->addData($data);
        }
    }
    public function getExtendDataMsg($name,$type="json",$withOri=true,$replace=true)
    {
        $datamsg = null;
        $result = array();
        for($i=0;$i<$this->getSize();$i++)
        {
            $data = $this->getData($i);
            if($type =="cvs")
            {
                if($this->csvTitle!=null)
                {
                    $data->setCvsTitle($this->csvTitle);
                }
                if($this->csvDbTitle!=null)
                {
                    $data->setCvsDbTitle($this->csvDbTitle);
                }
            }
            $data->getExtendData($name,$type,$withOri,$replace);
            $result[] = $data;
        }
        if($replace)
        {
            $this->setResult($result);
            $datamsg = $this;
        }
        else {
            $datamsg = new DataMsg();
            if($this->db!=null)
            {
                $datamsg->setDb($this->db);
                $datamsg->setResult($result);
            }
        }
        return $datamsg;
    }
}



?>