<?php
  require_once(dirname(__FILE__)."/commonTools.php");
  require_once(dirname(__FILE__)."/baseVo.php");
  require_once(dirname(__FILE__)."/quickCrypt.php");
  require_once(dirname(__FILE__)."/quickFormConfig.php");


 
 class Data extends BaseVo{
     
      protected $tableSign = null;
      protected $encode = null;
      protected $subTable = array();
      protected $primaryKey = array();
      protected $colType = Array();
      protected $tableName = "";
      protected $db = null;
      protected $whereClause = null;
      protected $orderClause = null;
      protected $pdoMode = false;
      protected $itemMap = null;
      protected $itemList = null;
      protected $csvTitle = null;
      protected $csvDbTitle = null;
      protected $dataDiff = array();
      protected $sql = null;
      protected $baseSql = null;
      protected $where = null;
      protected $tables = array();
      protected $cols = null;
      protected $sqlBuilder = null;
      protected $forceAdd = false;
      protected $colInfoArray = array();
      protected $colStyle = Array();
      const COLTYPE_DATAONLY = 0;
      const COLTYPE_WHEREONLY = 1;
      const COLTYPE_BOTH = 2;
      protected $protectStatus = true;
      protected $operator = array();
      protected $searchResult = false;
      protected $isVerticalTable = false;
      protected $vtKeyCol = null;
      protected $vtValueCol = null; 
      protected $vtIdCol = null;
      protected $whereArray = Array();
      protected $whereMixMode = true;
      protected $cryptInfo = Array();
      public function setWhereMixMode($whereMixMode)
      {
          $this->whereMixMode = $whereMixMode;
      }
      public function getWhereMixMode()
      {
          return $this->whereMixMode;
      }
      public function getWhereArray($baseOnColType=true)
      {
          $result = $this->whereArray;

          if(!$baseOnColType)
          {
              foreach($this->getDataArray() as $key =>$value)
              {
                if($this->colType[$key]==self::COLTYPE_DATAONLY&&!isset($this->whereArray[$key]))
                {
                    $result[$key] = $value;
                }
              }
          }
          return $result;
      }
      public function isVt()
      {
        return $this->isVerticalTable;
      }
      public function getVtKeyCol()
      {
         return $this->vtKeyCol;
      }
      public function getVtValueCol()
      {
         return $this->vtValueCol;
      }
      public function getVtIdCol()
      {
         return $this->vtIdCol;
      }
      public function setVtInfo($vtIdCol,$vtKeyCol,$vtValueCol)
      {
          $this->isVerticalTable = true;
          $this->vtIdCol = $vtIdCol;
          $this->vtKeyCol = $vtKeyCol;
          $this->vtValueCol = $vtValueCol;
      }

      public function closeVtMode()
      {
            $this->isVerticalTable = false;
      }
     
      public function setKeepOri($key,$keepOri)
      {
          $this->colStyle[$key]["keepOri"] = $keepOri;
      }

      public function setCryptInfo($name,$key=null,$fixkey=false,$canDecrypt=true)
      {
         if($key==null||trim($key)=="")
         {
              $key = $name;
              $fixkey = false;
         }
          $this->cryptInfo[$name] = Array("key"=>$key,"fixkey"=>$fixkey,"canDecrypt"=>$canDecrypt);
      }   

      public function getDecryptString($name)
      {
        $value = $this->getString($name);
        $cryptInfo = $this->cryptInfo[$name];
       
        $canDecrypt = $cryptInfo["canDecrypt"];
        if($canDecrypt)
        {
           $key = $cryptInfo["key"];
           $fixkey = $cryptInfo["fixkey"]; 
           if(!$fixkey)
           {
              $key = $this->getString($key);
           }

           $quickCrypt =  new QuickCrypt(md5(strval($key)),sha1(strval($key)));
           $value = $quickCrypt->decrypt($value);
        }
        return $value;
      }

      public function getEncryptString($name,$value)
      {
        
        if($this->cryptInfo[$name]!=null&&isset($this->cryptInfo[$name])&&is_array($this->cryptInfo[$name]))
        {
          $cryptInfo = $this->cryptInfo[$name];
          $canDecrypt = $cryptInfo["canDecrypt"];
          $key = $cryptInfo["key"];
          $fixkey = $cryptInfo["fixkey"]; 
          if(!$fixkey)
          {
             $key = $this->getString($key);
          }
          $quickCrypt =  new QuickCrypt(md5(strval($key)),sha1(strval($key)));
          $value = $quickCrypt->encrypt($value);
          if(!$canDecrypt)
          {
            //echo 111;
            $value = sha1($value);
          }
        }
        return $value;
      }


      public function set($name,$value,$colType = self::COLTYPE_BOTH)
      {
         $this->colStyle[$name] = Array();
         if(is_string($value))
         {
            $value = StringTools::conv($value,$this->encode);
         }
         $this->setWithOperator($name,$value,"=",$colType);
      }

      public function setJsonArray($name,$array,$colType = self::COLTYPE_BOTH)
      {
          $value = ArrayTools::arrayToJson($array);
          $this->set($name,$value,$colType);
      }
      public function setImage($name,$filepath)
      {
              $value = "(SELECT * FROM   openrowset(bulk   N'".$filepath."',single_blob) AS ".$name.")";
              $this->colStyle[$name] = Array("keepOri"=>true);
              $this->setWithOperator($name,$value,"=",$colType);
      }
      public function haveSubTable($tableSign=null)
      {
        $result = false;
        if($tableSign==null)
        { 
           if(count($this->subTable)>0)
           {
              $result = true;
           }
        }
        else
        {
           $result = is_array($this->subTable[$tableSign]);
        }
         return $result;
      }

      public function setTableSign($tableSign)
      {
         $this->tableSign = $tableSign;
      }

      public function getTableSign()
      {  
        $result = $this->tableSign;
        if($this->isVt())
        {
          $result = "vtmain_".$this->getTableName();
        }
        return $result;
      }

      public function setSubTable($tableSign,$tableName,$primaryKey,$onClause,$relation = "LEFT JOIN")
      {
          $this->subTable[$tableSign] =  Array("tableSign"=>$tableSign,"tableName"=>$tableName,"primaryKey" => $primaryKey,"relation" => $relation,"onClause"=>$onClause);
      }
      public function setSubTables($subTable)
      {
         $this->subTable = $subTable;
      }
      public function getSubTables()
      {
         return $this->subTable;
      }

      public function getSubData($tableSign)
      {
          $result = null;
          $subTableInfo = $this->subTable[$tableSign];
          if(is_array($subTableInfo))
          {
              $data = new Data($this->getDb(),$subTableInfo["tableName"],$subTableInfo["primaryKey"]);
              $sign = $tableSign."_";
              $dataArray = $this->getDataArray();
              $newDataArray = CommonTools::getDataArray($dataArray,$sign);
              $data->setDataArray($newDataArray);
              $result = $data;
          }
          return $result;

      }

      public function getUnitedSql()
      {
          
          $mainSign = $this->getTableSign();
          $mainTableName = $this->getTableName();
          $mainDetail = $this->getColDetail();
          $sql = "SELECT ".$this->getColsStringFromTableDetail($mainSign,$mainDetail,false);
          $from = " FROM ".$this->getDb()->processTableObj($mainTableName)." ".$mainSign." ";
          foreach($this->subTable as $subTableSign => $subTableInfo)
          {
              $subTableName = $subTableInfo["tableName"];
              $subTableDetail = $this->getColDetail($subTableName);
              $sql.=",".$this->getColsStringFromTableDetail($subTableSign,$subTableDetail);
              $relation = $subTableInfo["relation"];
              $onClause = $subTableInfo["onClause"];
             // echo $onClause;
              $from .= " ".$relation." ".$this->getDb()->processTableObj($subTableName)." ".$subTableSign." ON ".$onClause." "; 
          }
          $sql = $sql.$from;
          return $sql;
      }

      protected function getColsStringFromTableDetail($tableSign,$tableDetail,$subTable=true)
      {
          $sql = "";
          foreach($tableDetail as $col =>$colInfo)
          {   
              $fullCol = $col;
              if($subTable)
              {
                $fullCol = $tableSign."_".$col;
              }
              $sql .=",".$tableSign.".".$this->getDb()->processTableObj($col)." ".$this->getDb()->processTableObj($fullCol)." ";
          }
          $sql =ltrim($sql,",");
          return $sql;
      }

      public function getSearchResult()
      {
         return $this->searchResult;
      }
    

      public function setMainTable($mainTable)
      {
        $this->mainTable = $mainTable;
      }

      public function checkDb($autoRepair=false)
      {
         $sql = "SHOW TABLES";
         //echo $sql;
         $datamsg = new DataMsg();

         $datamsg->findBySql($this->getDb(),$sql);

         for($i=0;$i<$datamsg->getSize();$i++)
         {
      
            $data = $datamsg->getData($i);
            $dataArray = $data->getDataArray();
            foreach($dataArray as $key=>$value)
            {
              
              $this->checkTable($value);
            }
         }
      }


      public function checkTable($autoRepair=false)
      {
          $sql = "CHECK TABLE ".$this->getTableName();
          $this->findBySql($sql);
          $checkResult = $this->getString("Msg_text");
          $result = false;
          if(strtoupper(trim($checkResult))=="OK")
          {
              $result = true;
          }
          if($autoRepair)
          {
            if(!$result)
            {
               echo $this->getTableName()." has some problem,will try to repair it. the error information is ".$result."<br>";
               $this->repairTable();
            }
            else
            {
               echo $this->getTableName()." is fine.<br>";
            }
          }
          return $result;
      }

      public function getColDetail($tableName=null)
      {
         if($tableName==null)
         {
            $tableName = $this->getTableName();
         }
         $sql = "DESC ".$tableName;
        
         if($this->db instanceof sqlLite)
         {
          
            $sql = 'PRAGMA table_info('.$tableName.')';
         }
         else if($this->db->isMsSql())
         {
            $sql = 'exec sp_columns "'. $tableName .'"';
         }

         $dataMsg = new DataMsg();
         $dataMsg->findBySql($this->db,$sql);
         $resultArray = null;
         if($this->db instanceof sqlLite)
         {
             $resultArray = $dataMsg->getKeyDataArray("NAME",true,true,CASE_LOWER);
         }
         else if($this->db->isMsSql())
         {
           $resultArray = $dataMsg->getKeyDataArray("COLUMN_NAME",true,true,CASE_LOWER);
         }
         else
         {
           $resultArray = $dataMsg->getKeyDataArray("Field",true,true,CASE_LOWER);
         }
          
         $resultArray = array_change_key_case($resultArray,CASE_LOWER);
         $result = Array();
         foreach($resultArray as $key=>$data)
         {
           if($this->db instanceof sqlLite)
           {
              $type = $data["type"];
              $length = StringTools::cutString($type,"(",")",false);
              $data["field"] = strtolower($data["name"]);
              $data["default"] = $data["dflt_value"];
              $data["nullable"] = $data["notnull"];
              $result[$key] = $data;
           }
           if($this->db->isMsSql())
           {
              $data["type"] = $data["type_name"];
              $data["field"] = strtolower($data["column_name"]);
              $data["length"] = $data["length"];
              $result[$key] = $data;
           }
           else
           {
              $type = $data["type"];
              $length = StringTools::cutString($type,"(",")",false);
              $field = strtolower($data["field"]);
              $data["field"] = $field; 
              $data["length"] =  $length;
              $result[$key] = $data;
           }
         }
         return $result;
       }

       public function getSearchSql($sign=null,$colDetail=null)
       {
            if($sign ==null ||trim($sign)=="")
            {
                $sign = "a";
            }
            if($colDetail==null||!is_array($colDetail))
            {
                $colDetail = $this->getColDetail();
            }
            $sql = "SELECT";
            $cols = "";
            foreach($colDetail as $key=>$data)
            {
               $cols .= $sign.".".$this->getDb()->processTableObj($key).",";
            }
            $cols = trim($cols,",");
            $sql .= " ".$cols." FROM ".$this->getDb()->processTableObj($this->getTableName())." ".$sign;
            return $sql; 
       }

      
     

      public function repairTable()
      {
          $sql = "REPAIR TABLE ".$this->getTableName();
          return  $db->execSql($sql);
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
      public function setColInfoArray($colInfoArray)
      {
          $this->colInfoArray = $colInfoArray;
      }
      public function getColInfoArray()
      {
          return  $this->colInfoArray;
      }
      public function isForceAdd()
      {
         return $this->forceAdd;
      }
      public function setForceAdd($forceAdd)
      {
          $this->forceAdd = $forceAdd;
      }
      public function getCols()
      {
         if($this->cols==null)
         {
            $this->cols =  DbTools::getColNames($this->sql);
         }
         return $this->cols;
      }

      public function addCols($dbname,$realname)
      {
         if(!is_array($this->cols))
         {
            $this->cols = Array();
         }
         $this->cols[$dbname] = $realname;
      }
      public function setCols($cols)
      {
          $this->cols = $cols;
      }
      public function setSqlBuilder($sqlBuilder)
      {
          $this->sqlBuilder = $sqlBuilder;
          if($sqlBuilder!=null)
          {
            $this->where = $sqlBuilder->where;
            $this->cols = null;
            $this->sql = null;
          }
      }
      public function setWhere($where)
      {
          $this->where = $where;
          if($this->sqlBuilder!=null)
          {
              $sqlBuilder = $this->sqlBuilder;
              $sqlBuilder->where = $where;
              $this->sqlBuilder =  $sqlBuilder;
          }
          if($where!=null)
          {
            $this->whereClause = null;
          }
      }

      public function getWhere()
      {
         return $this->where;
      }

      public function getSqlBuilder()
      {
          return  $this->sqlBuilder;
      }

      public function getSql()
      {
          return $this->sql;
      }
      public function setSql($sql)
      {
          $this->sql = $sql;
          $this->cols = null;
          $this->sqlBuilder = null;
      }

      public function setColInfo($colinfo)
      {
          $this->cols = $colinfo;
      }

      public function getColInfo()
      {
         return $this->cols;
      }

      public function getTableKeyFormCol($col)
      {
          return DbTools::getTableKeyFormCol($col);
      } 

      public function getColNameFormCol($col)
      {
         return DbTools::getColNameFormCol($col);
      }  

      protected function getMtTableList($keys=null)
      {
          $tables = $this->tables;
          if($keys!=null)
          {
              $tables = array();
              $keys = explode(",",$keys);
              foreach($keys as $key)
              {
                  $tmp = $this->tables[$key];
                  if(is_array($tmp))
                  {
                      $tables[$key] = $tmp;
                  }
              }
          }

          return $tables;
      }

      public function getMtDataMsg($keys=null)
      {
          $tables = $this->getMtTableList($keys);
            
          $cols = $this->getCols();
          foreach($this->dataArray as $key => $value)
          {
              $col = $cols[$key];
             
              if($col!=null&&trim($col)!="")
              {
                  $tablekey = $this->getTableKeyFormCol(trim($col));
                  $tableinfo = $tables[$tablekey];
                  if(is_array($tableinfo))
                  {
                     $data = $tableinfo["data"];
                     $colname = $this->getColNameFormCol(trim($col));
                     $data[$colname] = $value;
                     $tables[$tablekey]["data"] = $data;
                  }
              }
          }
          $dataMsg = new DataMsg($this->db);
          foreach($tables as $key =>$tableinfo)
          {
             if($tableinfo["tablename"]!=null&&trim($tableinfo["tablename"])!=""&&$tableinfo["pk"]!=null&&trim($tableinfo["pk"])!=""&&is_array($tableinfo["data"])&&count($tableinfo["data"])>0)
             {
                $data = new Data($db,$tableinfo["tablename"],$tableinfo["pk"]);
                $data->setDataArray($tableinfo["data"]);
                if(isset($tableinfo["forceadd"])&&is_bool($tableinfo["forceadd"]))
                {
            
                    $data->setForceAdd($tableinfo["forceadd"]);
                }
                $data->setTableSign($key);
                $dataMsg->addData($data);
             }
          }
          return $dataMsg;
      }
      public function setTables($tables)
      {
          $this->tables = $tables;
      }
      public function setTable($pk,$tablename,$key=null,$data=array())
      {
         if($key==null)
         {
             $key = $tablename;
         }
         $this->tables[trim($key)] = array("pk"=>trim($pk),"tablename"=>trim($tablename),"data"=>$data);
      }

      public function addOrder($col,$desc=false)
      {
          $col = $this->getColKeyString($col);
          $orderClause = "";
          if($this->orderClause!=null)
          {
              $orderClause.=$this->orderClause.",";
          }
          $orderClause .= " ".$col;
          if($desc)
          {
             $orderClause .= " DESC";
          }
          $this->orderClause = $orderClause;
      }

      public function setBaseVo($baseVo)
      {
           $this->setDataArray($baseVo->getDataArray());
      }
      
      public function getBaseVo()
      {
           $baseBo = new BaseVo();
           $baseBo->setDataArray($this->getDataArray());
      } 

      public function getColType($name)
      {
        return $this->colType[$name];
      }

      public function getDataDiff()
      {
          return $this->dataDiff;
      }
      public function clearDataDiff()
      {
          $this->dataDiff = array();
      }
      public function setCsvTitle($csvTitle)
      {
          $this->csvTitle = $csvTitle;
      }
      public function hasData()
      {
          $result = false;
          if($this->dataArray!=null&&count($this->dataArray)>0)
          {
              $result = true;
          }
          return $result;
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
      
     
      
      public function getItemSize()
      {
          return count($this->itemList);
      }
      public function addItem($name,$key,$method=null)
      {
          if($this->itemMap == null||$this->itemList == null)
          {
              $this->itemMap = array();
              $this->itemList = array();
          }
          $this->itemMap[$key]["name"] = $name;
          $this->itemList = $key;
          if($method!=null)
          {
               $this->itemMap[$key]["method"] = $key;
          } 
      }
      public function clearItem()
      {
           $this->itemMap = array();
           $this->itemList = array();
      }
      
      public function getItemName($i)
      {
             return $this->getItemValueByKey($this->itemList[$i]);
      }
      
      public function getItemValue($i)
      {
          return $this->getItemNameByKey($this->itemList[$i]);
      }
      
      
      public function getItemNameByKey($key)
      {
          return  $this->itemMap[$key]["name"];
      }
      
      public function getItemValueByKey($key,$export)
      {
          $result = $this->getString($key);
          if(isset($this->itemMap[$key]["method"]))
          {
              if($this->itemMap[$key]["method"]!=null&&trim($this->itemMap[$key]["method"])!="")
              {
                  $method = $this->itemMap[$key]["method"];
              }
              if($method!=null&&trim($method)!="")
              {
                  $result = $this->$method($key,$export);
              }
          }
      }
      
      public function setPdoMode($pdoMode=true)
      {
          $this->pdoMode = $pdoMode;
      }
      


      function __construct($db,$tableName="",$primaryKey=null,$src=null,$prefix=null,$blank=true) 
      {
          
           $this->db = $db;
           $this->tableSign = "main";
           $this->tableName = $tableName;
           $encode = QuickFormConfig::$dbEncode;
           if($encode==null&&trim($encode)=="")
           {
              $encode = QuickFormConfig::$encode;
           }
           $this->encode = $encode;
           if($primaryKey!=null)
           {
              $this->primaryKey[] = $primaryKey;
           }
           if($src!=null&&$prefix!=null)
           {
               $this->createFormData($src,$prefix,$blank);
           }
      }
      
      public function loadJsonString($json)
      {
          $arr = json_decode($json);
          foreach($arr as $key=>$value)
          {
              $this->$key = $value;
          }
      }
      
      public function remove($key)
      {
          unset($this->dataArray[$key]);
          unset($this->whereArray[$key]);
          unset($this->colType[$key]);
      }
      public function getJsonString($autoDecode=true)
      {
          $result =  ArrayTools::arrayToJson($this->dataArray,"urlencode",true,$autoDecode); 
          return $result;
      }
      public function setTran($name,$value,$prefix="tr_")
      {
          $newName = $prefix.$name;
          $this->set($newName,$value);
      } 
      public function getTran($name,$prefix="tr_")
      {
          $newName = $prefix.$name;
          return $this->get($newName);
      }
      public function setDb($db)
      {
          
          $this->db = $db;   
      }
      
      public function formatData($upper=false)
      {
          $case = CASE_LOWER;
          if($upper)
          {
                $case = CASE_UPPER;
          }
          $this->dataArray = array_change_key_case($this->dataArray,$case);
      }
      public function setWhereOnly($name,$value,$operator="=")
      {
        $this->setWithOperator($name,$value,$operator,self::COLTYPE_WHEREONLY);
      }
       public function setDataOnly($name,$value)
      {
        $this->setWithOperator($name,$value,"=",self::COLTYPE_DATAONLY);
      } 
     
      public function setWithOperator($name,$value,$operator="=",$colType = self::COLTYPE_WHEREONLY)
      {
         if($value==0)
         {
             $value = strval($value);
         }

         if($colType!=self::COLTYPE_WHEREONLY)
         {
           $this->dataArray[$name] = $value;
         }
        
         if($colType!=self::COLTYPE_DATAONLY)
         {
            $this->whereArray[$name] = $value;
            $this->operator[$name] = $operator;
         }
         
         $this->colType[$name] = $colType;
      }

      
     
     
     public function getValue($name,$isTableItem=true,$isExoprt=false,$isMethod=false,$methodName="")
     {
       
         if($isTableItem&&$isMethod)
         {
               return $this->$methodName($name,$isExoprt);
         }
         return $this->get($name);
     }
      
    
     public function getJsonArray($name,$returnNull=true)
     {
          $value = $this->getString($name);
          $result = Array();
          if($returnNull==true)
          {
              $result = null;
          }    
          if($value!=null&&trim($value)!="")
          {
              $result = ArrayTools::jsonToArray($value);
          }
         return $result;
     }
     public function getCsvArray($name,$withName=true,$dbname=true,$returnNull=true)
     {
         $value = $this->getString($name);
         return $this->getCsvArrayByString($value,$withName,$dbname,$returnNull);
         
     }
     
     public function getCvsData($name,$withOri=true,$replace=true)
     {
         return $this->getExtendData($name,"cvs",$withOri,$replace);
     }
     public function getJsonData($name,$withOri=true,$replace=true)
     {
          return $this->getExtendData($name,"json",$withOri,$replace);
     }
     protected function getExtendData($name,$type="json",$withOri=true,$replace=true)
     {
         $extend = null;
         if(trim(strtolower($type))=="json")
         {
             $extend = $this->getJsonArray($name);
         }
         else
         {
             $extend = $this->getCsvArray($name);
         }
         $dataArray = $this->getDataArray();
         if($extend!=null)
         {
            if($withOri)
            {
                $dataArray = array_merge($dataArray,$extend);
            }
            else {
                $dataArray = $extend;
            }
         }
         if($replace)
         {
             $this->setDataArray($dataArray);
             return $this;
         }
         else {
             $newData = $this;
             $newData->setDataArray($dataArray);
             return $newData;
         }
     }
   
      public function getCsvArrayByString($value,$withName=true,$dbname=true,$returnNull=true)
      {
          $result = Array();
          if($returnNull==true)
          {
              $result = null;
          }    
          if($value!=null&&trim($value)!="")
          {
             $result = str_getcsv($value);
             if($withName)
             {
               
                 $title = $this->csvTitle;
                 if($dbname)
                 {
                     $title = $this->csvDbTitle;
                 }

                 $title = explode(",",$title);

                 if($title!=null&&is_array($title))
                 {  

                   
                    $temp = $result; 
                    $result = Array();
                    $count = count($title);
                    $t = count($temp);
                    if($count>$t)
                    {
                        $count = $t;
                    }
                    for($i=0;$i<$count;$i++)
                    {
             
                        $result[trim($title[$i],'"')] =  trim($temp[$i],'"');           
                    } 
                 }
             }
          }  
          return $result; 
      }

     public function getCsvData($name,$tableName="",$primaryKey="")
     {
         $value = $this->geString($name);
         return $this->getCsvDataByString($value,$tableName,$primaryKey);
         
     }

      public function getCsvDataByString($string,$tableName="",$primaryKey="")
      {
          $data = null;
          $dataArray = $this->getCsvArrayByString($string);
          if($dataArray!=null)
          {
             $data = new Data($this->db,$tableName,$primaryKey);
             $data->setDataArray($dataArray);
          }
          return $data;
      }
      
      public function setCsvArray($array,$withName=false)
      {
          if($withName)
          {
              $temp = $array;
              $array = Array();
              foreach($temp as $name=>$value)
              {
                  $array[] = $value;
              }
          }
         $title = $this->csvDbTitle;
         if($title!=null&&is_array($title))
         {
             $count = count($title);
             $t = count($temp);
             if($count>$t)
             {
                 $count = $t;
             } 
             for($i=0;$i<$count;$i++)
             {
                 $this->set($this->csvDbTitle[$count],$array[$count]);
             }   
         }
      }
      
      public function setCsvString($string)
      {
          return $this->setCsvArray(str_getcsv($string));
      }
      
      
      public function getString($name,$defaultValue="",$allowEmpty=false)
      {
          $value = $this->get($name,$defaultValue);   
          if(!$allowEmpty&&trim($value)=="")
          {
              $value =  $defaultValue;
          }
          return $value;
      }
      
      public function getFloat($name,$defaultValue=0.00,$precision=null)
      {
          $value = $this->get($name,null); 
          $result = floatval($defaultValue);
          if($value!=null&&trim($value)!="")
          {
                $result = floatval($value);
          }
          if($precision!=null&&is_int($precision))
          {
              $result = round($result,$precision);
          }
          return $result;
      }
      
      public function getInt($name,$defaultValue=0)
      {
          $value = $this->get($name,null); 
          $result = intval($defaultValue);
          if($value!=null&&trim($value)!="")
          {
                $result = intval($value);
          }
          return $result;
      }
      
      
      public function getPrimaryKey()
      {
          return $this->primaryKey;
      }
      public function addPrimaryKey($key)
      {
          $this->primaryKey = array_merge($this->primaryKey,array($key,));
      }

    

      protected function getPKWhereClause($pdoMode=false)
      {
          $sql = "1 = 1";
          $primaryKey=$this->getPrimaryKey();
         
          for($i=0;$i<count($primaryKey);$i++)
          {
             $key =$primaryKey[$i];
             $colKey = $this->getColKeyString($key);
             $value = $this->get($key);
             $operator =trim($this->operator[$key]);
             $pk = false;
             if($operator=="="||$operator == null || $operator=="")
             {
                
                 $pk = true;
             }
             if($value!=null&&trim($value)!=""&&$pk)
             {

               if($pdoMode)
               {
                     $sql .= " AND ".$colKey." = :".$key." ";
               }
               else {

                       if(is_bool($this->colStyle[$key]["keepOri"])&&$this->colStyle[$key]["keepOri"])
                       {
                              $sql .= " AND ".$colKey." = ".$value." ";
                       }
                       else
                       {
                          $db =  $this->getDb();
                          if($db->isMsSql())
                          {
                              $value = str_replace("'","''",$value);
                          }
                          else
                          {
                             $value = addslashes($value);
                          }
                          $sql .= " AND ".$colKey." = '".$value."' ";
                       }     
               }
             }
          }
          return $sql;
      }
      
      protected function bindPKValue($stmt)
      {
           $primaryKey=$this->getPrimaryKey();
           for($i=0;$i<count($primaryKey);$i++)
           {
             $key =$primaryKey[$i] ;
             $value = $this->get($key);
             $stmt->bindValue(':'.$key, $value);
           }
           return $stmt;
      }
      public function setTableName($tableName)
      {
          $this->tableName = $tableName;
      }
      public function getTableName()
      {
          return $this->tableName;
      }
      public function setWhereClause($whereClause)
     {
         $this->whereClause = $whereClause; 
         $this->setWhere(null);
     }     
     public function isEmpty()
     {
         if(count($this->getDataArray())>0)
         {
             return false;
         }
         return true;
     }

     public function getSubTableSign($colkey)
     {
        $result = null;
        if($this->haveSubTable())
        {
                  $tmp = stripos($colkey,"_");
                  if($tmp)
                  {
                     $sign = substr($colkey,0,$tmp);
                     if($this->haveSubTable($sign))
                     {
                       $result = $sign;
                     }
                  }
        }
        return $sign;
     }

     

     public function getColKey($colkey)
     {
              $result = $colkey;
              if($this->haveSubTable())
               {
                  $tmp = stripos($colkey,"_");
                  if($tmp)
                  {
                     $sign = substr($colkey,0,$tmp);
                     if($this->haveSubTable($sign))
                     {
                        $result = Array(
                                        "tableSign" =>substr($colkey,0,$tmp),
                                        "colKey" =>substr($colkey,$tmp+1)
                                        );
                     }
                  }
                  else 
                  { 
                      $result = Array(
                                      "tableSign" =>$this->getTableSign(),
                                       "colKey" =>$colkey
                                      );
                  }
               }
               return $result;
     }

     public function isMainTableCol($colkey)
     {
      $result = true;
       if($this->haveSubTable())
       {

         $tmp = stripos($colkey,"_");
         if($tmp)
         {
            $sign = substr($colkey,0,$tmp);
            if($this->haveSubTable($sign))
            {
                  $result = false;
            }
         }
       }
       return $result;
     }


     public function isVtCol($colkey)
     {
        $result = false;
        if($this->isMainTableCol($colkey))
        {
            $result = true;
            foreach($this->getColDetail() as $colInfo)
            {
               if($colInfo["column_name"]==$colkey)
               {
                  $result = false;
                  break;
               }
            }
        }
        return $result;
     }

     public function getColKeyString($colKey)
     {
     
        $colKey = $this->getColKey($colKey);
        if(is_array($colKey))
        {
            $colKey = $colKey["tableSign"].".".$this->getDb()->processTableObj($colKey["colKey"]);
        }
        else
        {
            $colKey = $this->getDb()->processTableObj($colKey);
        }
        return $colKey;
     }

     protected function getOperator($key,$default="=")
     {
        $result = $default;
        if($this->operator[$key]!=null&&trim($this->operator[$key])!="")
        {
          $result = $this->operator[$key];
        }
        return $result;
     }


     public function getVtMainIdSql($baseOnColType=true)
     {
          $primarykeys = $this->getPrimaryKey();
        $vtsign = "vtmain_".$this->getTableName();
        $sql = "SELECT ".$vtsign.".".$this->getDb()->processTableObj($this->getVtIdCol())." FROM ".$this->getDb()->processTableObj($this->getTableName())." ".$vtsign;
        $signMark = false;
        $whereClause =$this->whereClause;
        if($this->whereClause==null||!$this->getWhereMixMode())
            {
               $whereClause = " 1 = 1 ";
            }
          foreach($this->getWhereArray($baseOnColType) as $key =>$value)
          {
              $array = Array("IN","NOT IN");
              $withIn = false;
                    $operator = strtoupper(trim($this->getOperator($key)));
                    if(in_array(TRIM(strtoupper($operator)), $array))
                    {
                      $withIn = true;
                    }
                  
                           if($value!=null)
                           {
                             $colKey = $this->getColKeyString($key);
                         
                              if($this->isVTCol($key)&&$this->isVt())
                              {
                              
                                $vktSign = $vtsign;
                                if($key == $this->getVtIdCol())
                                {
                                  $colKey = $vktSign.".".$this->getDb()->processTableObj($this->getVtIdCol());
                                }
                                else
                                {
                                  if($signMark)
                                  {
                                    $vktSign.="_".$key;
                                    $sql .= " LEFT JOIN ".$this->getDb()->processTableObj($this->getTableName())." ".$vktSign ;
                                      $sql .= " ON ".$vtsign.".".$this->getDb()->processTableObj($this->getVtIdCol())." = ".$vktSign.".".$this->getDb()->processTableObj($this->getVtIdCol())." ";
                                  }
                                  else
                                  {                                
                                    $signMark = true;
                                  }
                                    $db =  $this->getDb();
                                    $kv = $key;
                                  if($db->isMsSql())
                                  {
                                      $kv = str_replace("'","''",$kv);
                                  }
                                  else
                                  {
                                     $kv = addslashes($kv);
                                  }
                                    $whereClause .=" AND ".$vktSign.".".$this->getDb()->processTableObj($this->getVtKeyCol())." = '".$kv."'";
                                    $colKey = $vktSign.".".$this->getDb()->processTableObj($this->getVtValueCol());
                                }
                              }
                             if($withIn||(is_bool($this->colStyle[$key]["keepOri"])&&$this->colStyle[$key]["keepOri"]))
                             {
                               $whereClause .=" AND ".$this->getDb()->processTableObj($colKey)." ".$this->getOperator($key)." ".$value." ";
                             }
                             else
                             {
                                $db =  $this->getDb();
                                if($db->isMsSql())
                                {
                                    $value = str_replace("'","''",$value);
                                }
                                else
                                {
                                   $value = addslashes($value);
                                }
                                $whereClause .=" AND ".$colKey." ".$this->getOperator($key)." '".$value."' ";
                             }    

                           }
                     
           }
             
              foreach($this->subTable as $subTableSign => $subTableInfo)
             {

                      $subTableName = $subTableInfo["tableName"];
                      $relation = $subTableInfo["relation"];
                      $onClause = $subTableInfo["onClause"];
                      $sql .= " ".$relation." ".$this->getDb()->processTableObj($subTableName)." ".$subTableSign." ON ".$onClause." "; 
                }
              $sql.= " WHERE ".$whereClause;
              $sql.= " GROUP BY ".$vtsign.".".$this->getDb()->processTableObj($this->getVtIdCol());
              if($this->getOrderClause()!=null)
                      {
                    $sql.=" ORDER BY ".$this->getOrderClause();
                      }

                      return $sql;
        


     }
     
     public function getWhereClause($pdo=false,$baseOnColType=true)
     {
         $whereClause =$this->whereClause;
         if($this->whereClause==null||$this->getWhereMixMode())
         {
          $whereClause = " 1 = 1 ";
          if($this->whereClause!=null&&trim($this->whereClause)!="")
          {
            $whereClause = $this->whereClause;
          } 
             foreach($this->getWhereArray($baseOnColType) as $key => $value)
             {
               $colKey = $this->getColKeyString($key);
               $array = Array("IN","NOT IN");
               $operator = strtoupper(trim($this->getOperator($key)));
               $withIn = false;
               if(in_array(TRIM(strtoupper($operator)), $array))
               {
                  $withIn = true;
               }   
                 if($pdo)
                 {
                      $whereClause .=" AND ".$colKey." ".$this->getOperator($key)." :".$key." ";
                 }
                 else {
                    if($value!=null)
                    {
                       if($withIn||(isset($this->colStyle[$key]["keepOri"])&&is_bool($this->colStyle[$key]["keepOri"])&&$this->colStyle[$key]["keepOri"]))
                       {
                          $whereClause .=" AND ".$colKey." ".$this->getOperator($key)." ".$value." ";
                       }
                       else
                       {
                          $db =  $this->getDb();
                          if($db->isMsSql())
                          {
                              $value = str_replace("'","''",$value);
                          }
                          else
                          {
                             $value = addslashes($value);
                          }
                          $whereClause .=" AND ".$colKey." ".$this->getOperator($key)." '".$value."' ";
                       }    
                    }
                 }
              
             }
         }
         return $whereClause;
     }
     public function getOrderClause()
     {
         return $this->orderClause;
     }
     public function setOrderClause($orderClause)
     {
         $this->orderClause = $orderClause;
     }
     
      public function createFormData($src,$prefix,$blank=true)
      {
    
          $array = CommonTools::getDataArray($src,$prefix,$blank);
          $this->setDataArray($array);
          return $this;
      }
      
      public function find($pagerows=0,$curpage=1,$getSql=false)
      {
          $dataMsg = new DataMsg();
          return $dataMsg->findByData($this,$pagerows,$curpage,$getSql);
      }


      
      public function findBySql($sql)
      {             
          $db = $this->db;
              if($this->pdoMode)
              {
                  $pdo = $db;
                  $stmt = $pdo->prepare($sql);
                  $stmt = $this->bindPKValue($stmt);
                  $this->searchResult = $stmt->execute();
                  if($this->searchResult)
                  {
                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    $this->setDataArray($stmt->fetchAll());
                  }
              }
              else {
                    $db->openQuery($sql);
                    if($db->getResult()){
                           $this->searchResult = true;
                           foreach($db->getResult() as $r)
                           {
                              $this->setDataArray($r);
                           }
                           /*if($db->isMsSql())
                           {
                              $func = "mssql_fetch_array";
                              $para = MSSQL_ASSOC;
                              if($db->isSqlSrv())
                              {
                                  $func = "sqlsrv_fetch_array";
                                  $para = SQLSRV_FETCH_ASSOC;
                                   
                              }

                               while($row_data= $func($db->result,$para)){
                                 
                                    $this->setDataArray($row_data);
                               }           
                           }
                           else {
                                while($row_data=mysql_fetch_assoc($db->result)){
                                    $this->setDataArray($row_data);
                               }            
                           } */     
                    }

              }
         
          return $this;
      }






    

      
      public function findByPrimaryKey($getSql=false)
      {
         
          $sql = "SELECT * FROM ".$this->getDb()->processTableObj($this->getTableName());
          if($this->haveSubTable())
          {
               $sql =  $this->getUnitedSql();
          }
          $sql .= " WHERE ";
          $whereClause = $this->getPKWhereClause($this->pdoMode);
          if($whereClause == "1 = 1")
          {
               return false;
          }
          else
          {
              $sql .= $whereClause;
          }

          if($getSql)
          {
             return $sql;
          } 
          return $this->findBySql($sql);
      }
     public function deleteByPrimaryKey($getSql=false,$getRecord=false)
     {
          return $this->delete($getSql,$getRecord,true);
     }

     protected function getVtWhere($byPrimaryKey=false)
     {
        $where = null;
        $vtIdCol = $this->getVtIdCol();
        $vtValueCol = $this->getVtValueCol();
        $vtKeyCol = $this->getVtKeyCol();
        $whereArray = $this->getWhereArray();

        $keycount = count($whereArray);
        if($keycount==1&&isset($whereArray[$vtIdCol]))
        {
          $byPrimaryKey = true;
        }
        if($byPrimaryKey)
        {
            $vtId = $this->get($vtIdCol);
            $array = Array("IN","NOT IN");
            $operator = strtoupper(trim($this->getOperator($vtIdCol)));
            $withIn = false;
            if(in_array(TRIM(strtoupper($operator)), $array))
            {
               $withIn = true;
            }
            if($vtId!=null&&trim($vtId)!="")
            {
              if($withIn||(is_bool($this->colStyle[$vtIdCol]["keepOri"])&&$this->colStyle[$vtIdCol]["keepOri"]))
              {
                  $where = $this->getDb()->processTableObj($vtIdCol) ." ".$this->getOperator($vtIdCol)." ".$vtId;
              }
              else
              {
                $where = $this->getDb()->processTableObj($vtIdCol) ." ".$this->getOperator($vtIdCol)." '".$vtId."'";
              } 
            }
        } 
        else
        {
           if($keycount>0)
           {
               $sql = $this->getVtMainIdSql();
               $where = $this->getDb()->processTableObj($vtIdCol) . " IN (".$sql.")";
           }
        }
        return $where;
     }

     protected function deleteVt($getSql=false,$getRecord=false,$byPrimaryKey=false)
     {  
        if(!$this->isVt())
        {
           return $this->delete($getSql,$getRecord,$byPrimaryKey);
        }
        $where = $this->getVtWhere($byPrimaryKey);
        if($this->getProtectStatus()&&$where==null)
        {
          echo "ERROR:<br>Your sql is:".$sql.", it's will delete all records in table ".$this->getTableName().",if you make sure you want to do this operation ,please use function closeProtectStatus() for skip this error.<br>";
          die();
        }
        else
        {
          $sql = "DELETE FROM ".$this->getDb()->processTableObj($this->getTableName())." WHERE "
                .$where;
           if($getSql)
           {
             return $sql;
           }
           else
           {
             $db = $this->db;      
             $result = $db->execSql($sql); 
             return $result;
           }
        }
     }

     public function delete($getSql=false,$getRecord=false,$byPrimaryKey=false)
     {
         

         $sqlQuery = "SELECT * FROM ".$this->getDb()->processTableObj($this->getTableName()). " WHERE "
         .$this->getWhereClause();
        
         if($byPrimaryKey)
         {
             if($whereClause == "1 = 1")
             {
                return false;
             }
             $sqlQuery = "SELECT * FROM ".$this->getDb()->processTableObj($this->getTableName()). " WHERE ".$this->getPKWhereClause($this->pdoMode);
         }

             
         $oriDataMsg = null;
         if($getRecord&&!$getSql)
         {
            
             $oriDataMsg = new DataMsg();
             $oriDataMsg->findBySql($this->db,$sqlQuery);
         }
         if($this->isVt())
         {
            $result = $this->deleteVt($getSql,$getRecord,$byPrimaryKey);
         }
         else
         {
             $whereClause = $this->getPKWhereClause($this->pdoMode);
             if(!$byPrimaryKey)
             {
               $whereClause = $this->getWhereClause($this->pdoMode);
             }
             $sql = "DELETE FROM ".$this->getDb()->processTableObj($this->getTableName())." WHERE "
                    .$whereClause;
             if($this->getProtectStatus())
             {
               if($whereClause==null||trim($whereClause)=="1 = 1")
               {
                  echo "ERROR:<br>Your sql is:".$sql.", it's will delete all records in table ".$this->getTableName().",if you make sure you want to do this operation ,please use function closeProtectStatus() for skip this error.<br>";
                  die();
               }
              
             }
           if($getSql)
           {
               return $sql;
           }
           if($this->pdoMode)
           {
               $pdo = $this->db;
               $stmt = $pdo->prepare($sql);
               foreach ($this->getDataArray() as $key=>$value)
               {
                    $stmt->bindValue(':'.$key, $value);
               }
               $stmt->execute();
           }
           else
           {
             $db = $this->db;      
             $result = $db->execSql($sql); 
           }
         }
         if($getRecord&&!$getSql)
         { 
            if($oriDataMsg!=null&&$oriDataMsg->getSize()>0)
            {
              for($i=0;$i<$oriDataMsg->getSize();$i++)
              {
                 $oriData = $oriDataMsg->getData($i);
                 $recordArray = Array();
                 $recordArray["type"] = "Delete";
                 $recordArray["tablename"] = $oriData->getTableName();
                 $oriArray =  $oriData->getDataArray();
                 $primarykey = $oriData->getPrimaryKey();
                 foreach($oriArray as $key=>$value)
                 {
                    if(in_array($key,$primarykey))
                    {
                       $recordArray["primarykey"] = Array($key=>$value,);
                    }
                    else
                    {
                       $recordArray["data"][$key]["old"] = $value;
                    }
                 }
                 $this->dataDiff[] = $recordArray;
              }
            }
         }
         return $result;  
     }
     protected function updateCrypt($id)
     {
        $result = true;
        if(is_array($this->cryptInfo)&&count($this->cryptInfo)>0)
         {
            
            $data = new Data($this->getDb(),$this->getTableName(),$this->primaryKey[0]);
            $data->set($this->primaryKey[0],$id);
            foreach($this->cryptInfo as $key => $cryptInfo)
            {
                $data->setCryptInfo($key,$cryptInfo["key"],$cryptInfo["fixkey"],$cryptInfo["canDecrypt"]);
                $data->setDataOnly($key,$this->getString($key));
            }
            $result = $data->update();
           
         }
         return $result;
     }
     protected function pdoCreate($getSql=false)
     {
         $recordArray = Array();
         $recordArray["type"] = "Insert";
         $recordArray["tablename"] = $this->getTableName();
        
         $sql = "INSERT INTO ".$this->getDb()->processTableObj($this->getTableName())." ";
         $cols = "";
         $values = "";
          foreach ($this->getDataArray() as $key=>$value)
          {
              if(($value!=null&&trim($value)!="")||!in_array($key,$this->getPrimaryKey()))
              {
                $cols .= ",".$this->getDb()->processTableObj($key);
                $values .= ",:".$key." ";
              }
          }
          $cols = substr($cols, 1);
          $values = substr($values, 1);
          $sql.= "(".$cols.") VALUES (".$values.") ";
         if($getSql)
         {
             return $sql;
         }
         $pdo = $this->db;
        
         $stmt = $pdo->prepare($sql);
         foreach ($this->getDataArray() as $key=>$value)
         {
            if($value!=null)
            {
              $stmt->bindValue(':'.$key, $value);
              $recordArray["data"][$key]["new"] =  $value;
            }
            else
            {
              $stmt->bindValue(':'.$key, '');
              $recordArray["data"][$key]["new"] =  '';
            }
         }
         $stmt->execute();        
         $id =  $pdo->lastInsertId();
         if(!$this->updateCrypt($id))
         {
            $id = false;
         }
         $recordArray["primarykey"] =  Array($this->primaryKey[0]);
         if($getRecord&&!$getSql)
         {
              $this->dataDiff[] = $recordArray;
         }
         return $id;
     }
     public function voidEmpty($voidSpace=false)
     {
         foreach ($this->getDataArray() as $key=>$value)
         {
            if($voidSpace)
            {
              $value = trim($value);
            }
            if($value==null||$value=="")
            {

                $this->remove($key);
            }
         }
     }
    
     public function getNewId($uuid)
     { 
        $result = null;
        if($uuid)
        {
           mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
           $charid = strtoupper(md5(uniqid(rand(), true)));
           $hyphen = chr(45);// "-"
           $result = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
        }
        else
        {
           $idCol = $this->primaryKey[0];
           if($this->isVt())
           {
              $idCol = $this->getVtIdCol();
           }
           $sql = "SELECT MAX(".$this->getDb()->processTableObj($idCol).") id FROM ".$this->getDb()->processTableObj($this->getTableName());
           $result = $this->getUniInt($db,$sql,"id")+1;
        }
        return $result;
     }  

     public function createVt($getSql=false,$getRecord=false)
     {
       if(!$this->isVt())
       {
          return $this->create($getSql,$getRecord);
       }
       $result = false;
       $mainid = $this->get($this->getVtIdCol());
       if($mainid==null||trim($mainid)=="")
       {
          die("please set ".$this->getVtIdCol()." for create record in vertical table at first.");
       }
       else
       {
          $dataMsg = new DataMsg();
          $dataMsg->setDb($this->getDb());
          $vtColArray = Array();
          $simpleColArray =  Array();

          foreach($this->getDataArray() as $k => $v)
          {
             
            if($this->isVtCol($k))
            {
                $vtColArray[$k] = $v;

            }
            else
            {
                $simpleColArray[$k]=$v;
            }
          }

          foreach($vtColArray as $k => $v)
          {    

                $data = new Data($this->getDb(),$this->getTableName(),$this->primaryKey[0]);
                foreach($simpleColArray as $tk=>$tv)
                {
                  $data->set($tk,$tv);
                }
                $data->set($this->getVtKeyCol(),$k);
                $data->set($this->getVtValueCol(),$v);
                $dataMsg->addData($data);
              
          }
          if($dataMsg->getSize()>0)
          {   
            $result =  $dataMsg->batchCreate($getSql);
          } 
          else
          {
            die("Please set some value for create record in vertical table at first.");
          }
       }
       return $result;
     }


     public function create($getSql=false,$getRecord=false)
     {
         if($this->isVt())
         {
           return $this->createVt($getSql,$getRecord);
         }
         if($this->pdoMode)
         {
             return $this->pdoCreate($getSql,$getRecord=false);
         }
         $sql = "INSERT INTO ".$this->getDb()->processTableObj($this->getTableName())." ";
         $cols = "";
         $values = "";
         $recordArray = array();
         $recordArray["type"] = "Insert";
         $recordArray["tablename"] = $this->getTableName();
        
         foreach ($this->getDataArray() as $key=>$value)
         {
             if(($value!=null&&trim($value)!="")||!in_array($key,$this->getPrimaryKey()))
             {
                $cols .= ",".$this->getDb()->processTableObj($key);
                if($value!=null)
                { 

                    if((!is_bool($this->colStyle[$key]["keepOri"])||!$this->colStyle[$key]["keepOri"]))
                  {
                          $db =  $this->getDb();
                          if($db->isMsSql())
                          {
                              $value = str_replace("'","''",$value);
                          }
                          else
                          {
                             $value = addslashes($value);
                          }
                      $values .= ",'".$value."' ";
                  }
                  else
                  {

                     $values .=",".$value." ";
                  }
                  $recordArray["data"][$key]["new"] =  $value;
                }
                else {
                  $values .=",''";
                  $recordArray["data"][$key]["new"] =  '';
                }
            }
         }
          
          $cols = substr($cols, 1);
          $values = substr($values, 1);
          $sql.= "(".$cols.") VALUES (".$values.") ";
         if($getSql)
         {
             return $sql;
         }
          $db = $this->db;  
          $temp  = $db->execSql($sql);
       
          $id = null;
          if($temp)
          {
               $id = $db->getLastInsertRowID();
        
          }
          if(!$this->updateCrypt($id))
           {
              $id = false;
           }
          $recordArray["primarykey"] =  Array($this->primaryKey[0]);
          if($getRecord&&!$getSql)
          {
             $this->dataDiff[] = $recordArray;
          }
          if($temp&&$id==0)
          {
            $id = $temp;
          }
          return $id;
          
     }

     protected function pdoUpdate($getSql=false)
     {
         $sql = "UPDATE ".$this->getDb()->processTableObj($this->getTableName()). " SET ";
         $cols = "";
         foreach ($this->getDataArray() as $key=>$value)
          {
            if($this->colType[$key] != self::COLTYPE_WHEREONLY)
            {
              if($value!=null&&trim($value)!="")
              {
                if($this->cryptInfo[$key]!=null&&isset($this->cryptInfo[$key])&&is_array($this->cryptInfo[$key]))
                {
                   $value = $this->getEncryptString($key,$value);
                }
                $cols .= ", ".$this->getDb()->processTableObj($key)." = :".$key." ";
              }
              else 
              {
                  $cols .= ", ".$this->getDb()->processTableObj($key)." = '' ";
              }
            }
          } 
         $cols = substr($cols,1);
         $sql .= $cols;
         $pk = false;
         $whereClause = "";
         if($this->whereClause==null)
         {
            $pkwhere = $this->getPKWhereClause(true);
            if($pkwhere== "1 = 1")
            {
                $whereClause.=$this->getWhereClause(false,true);    
            }
            else
            {
               $whereClause.=$this->getPKWhereClause(true);
              $pk = true;
            }

         }
         else 
         {
           $whereClause.=$this->getWhereClause();  
         }
         $sql.= " WHERE ".$whereClause;
         if($this->getProtectStatus())
         {
           if($whereClause==null||trim($whereClause)=="1 = 1")
           {
              echo "ERROR:<br>Your sql is:".$sql.", it's will update all records in table ".$this->getTableName().",if you make sure you want to do this operation ,please use function closeProtectStatus() for skip this error.<br>";
              eof(1);
           }
          
         }
         if($getSql) 
         {
             return $sql;
         }
         $pdo = $this->db;
         $stmt = $pdo->prepare($sql);
         foreach ($this->getDataArray() as $key=>$value)
         {         
              $stmt->bindValue(':'.$key, $value);
         }
         if($pk)
         {
            $stmt = $this->bindPKValue($stmt);
         }
         return $stmt->execute();
         
     }

     public function updateByPrimaryKey($getSql=false,$getRecord=false)
     {
            return $this->update($getSql,$getRecord,true);
     }
     protected function updateVt($getSql=fasle,$getRecord=false,$byPrimaryKey = false)
     {
         if(!$this->isVt())
         {
            return $this->update($getSql,$getRecord,$byPrimaryKey);
         }
         $mainId = $this->get($this->getVtIdCol());
         $mainIdMode = false;
         $oldDataCol = Array();
         if($mainId!=null&trim($mainId)!="")
         {
            $mainIdMode = true;
            $oldData = new Data($this->getDb(),$this->getTableName(),$this->primaryKey[0]);
            $oldData->set($this->getVtIdCol(),$mainId);
            $oldMsg = $oldData->find();
            $oldDataCol = $oldMsg->getValueList($this->getVtKeyCol());
         }
         
          $sql = Array();
          $vtColArray = Array();
          $simpleColArray =  Array();
          foreach($this->getDataArray() as $k => $v)
          {
             
            if($this->isVtCol($k))
            {
                $vtColArray[$k] = $v;

            }
            else
            {
                $simpleColArray[$k]=$v;
            }
          }
         $needCreate = false;
         foreach($vtColArray as $key =>$value)
         {
           if(!in_array($key, $this->primaryKey)&&$key!=$this->getVtIdCol())
           {
              if(!$mainIdMode||in_array($key, $oldDataCol))
              {
                if((!is_bool($this->colStyle[$key]["keepOri"])||!$this->colStyle[$key]["keepOri"]))
                {
                    $value = "'".$value."'";
                }
                $s = "UPDATE ".$this->getDb()->processTableObj($this->getTableName())." SET ".$this->getDb()->processTableObj($this->getVtValueCol())." = ".$value;
                foreach($simpleColArray as $sk=>$sv)
                {
                  if((!is_bool($this->colStyle[$sk]["keepOri"])||!$this->colStyle[$sk]["keepOri"]))
                  {
                      $sv = "'".$sv."'";
                  }
                  $s.= ",".$this->getDb()->processTableObj($sk)." = ".$sv." ";
                }
                $s .= " WHERE ".$this->getDb()->processTableObj($this->getVtKeyCol())." = '".$key."'";
                $vtwhere = $this->getVtWhere($byPrimaryKey);
                if($vtwhere!=null&&trim($vtwhere)!="")
                {
                   $s .=" AND ".$vtwhere;
                }
                $sql[] = $s;
              }
              else
              {
                  $createData = new Data($this->getDb(),$this->getTableName(),$this->primaryKey[0]);
                  $createData->setVtInfo($this->getVtIdCol(),$this->getVtKeyCol(),$this->getVtValueCol());
                  $createData->set($key,$value);
                  $createData->set($this->getVtIdCol(),$mainId);
                  foreach($simpleColArray as $ck=>$cv)
                  {
                    $createData->set($ck,$cv);
                  } 
                  $tmp = $createData->create(true);
                  $sql = array_merge($sql,$tmp);
              }
           }
         }
         if(count($sql)>0)
         {
            if($getSql)
            {
               return $sql;
            }
            else
            {
               $dataMsg = new DataMsg();
               return $dataMsg->batchExec($db,$sql);
            }
         }
         else
         {
            die("Please set some value for update record in vertical table at first.");
         }

     }
     public function update($getSql=false,$getRecord=false,$byPrimaryKey = false)
     {
        
        $oriArray = null;
        $recordArray = array();
        $sqlQuery = "SELECT * FROM ".$this->getDb()->processTableObj($this->getTableName()). " ";
        if($this->whereClause==null)
        {
           $pkwhere = $this->getPKWhereClause();
            if($pkwhere== "1 = 1")
            {
                 if($byPrimaryKey)
                 {
                     return false;
                 }
                 $sqlQuery.=" WHERE ".$this->getWhereClause(false,true);    
            }
            else
            {
                $sqlQuery.=" WHERE ".$this->getPKWhereClause();  
            }
        }
        else 
        {
            $sqlQuery.=" WHERE ".$this->getWhereClause();  
        }
         if($getRecord&&!$getSql)
         {
            
             $oriDataMsg = new DataMsg();
             $oriDataMsg->findBySql($this->db,$sqlQuery);
             $oriArray = $oriDataMsg->getKeyDataArray($this->primaryKey[0],true);
         }
         $result = null;
         if($this->isVt())
         {
            $result = $this->updateVt($getSql,$getRecord,$byPrimaryKey);
         }
         else
         {
             if($this->pdoMode)
             {
                  $result = $this->pdoUpdate($getSql);
             }
             else
             {
                 $sql = "UPDATE ".$this->getDb()->processTableObj($this->getTableName()). " SET ";
                 $cols = "";
                 foreach ($this->getDataArray() as $key=>$value)
                  {
                    
                        if(!in_array($key, $this->primaryKey))
                        {
                            if($value!=null&&trim($value)!="")
                            {
                              if($this->cryptInfo[$key]!=null&&isset($this->cryptInfo[$key])&&is_array($this->cryptInfo[$key]))
                              {
                                  $value = $this->getEncryptString($key,$value);
                              }
                              if(is_bool($this->colStyle[$key]["keepOri"])&&$this->colStyle[$key]["keepOri"])
                             {
                                $cols .= ", ".$this->getDb()->processTableObj($key)." = ".$value." ";
                             }
                             else
                             {
                              $db =  $this->getDb();
                              if($db->isMsSql())
                              {
                                  $value = str_replace("'","''",$value);
                              }
                              else
                              {
                                 $value = addslashes($value);
                              }
                              $cols .= ", ".$this->getDb()->processTableObj($key)." = '".$value."' ";

                             }
                            }
                            else
                            {
                               $cols .= ", ".$this->getDb()->processTableObj($key)." = '' ";
                            }
                        }
                      
                  } 
                  $cols = substr($cols,1);
                  $sql .= $cols;
                  $whereClause = "";
                 if($this->whereClause==null)
                 {
                    $pkwhere = $this->getPKWhereClause();
               
                    if($pkwhere== "1 = 1")
                    {
                         $whereClause.=$this->getWhereClause(false,true);    
                    }
                    else
                    {
                        $whereClause.=$this->getPKWhereClause();  
                    }
                 }
                 else 
                 {
                   $whereClause.=$this->getWhereClause();  
                 }
                 $sql.= " WHERE ".$whereClause;
                 if($this->getProtectStatus())
                 {
                   if($whereClause==null||trim($whereClause)=="1 = 1")
                   {
                      echo "ERROR:<br>Your sql is:".$sql.", it's will update all records in table ".$this->getTableName().",if you make sure you want to do this operation ,please use function closeProtectStatus() for skip this error.<br>";
                      eof(1);
                   }
                  
                 }

                 if($getSql)
                 {
                    $result = $sql;
                 }
                 else
                 {
                    $db = $this->db;

                    $result = $db->execSql($sql);
                 }
             }
             if($getSql)
             {
                 return $result;
             }
          }
         
        if($getRecord&&!$getSql)
        { 
            $resultArray = array();   
            $newArray = $this->getDataArray();
            foreach($oriArray as $id=>$oriData)
            {
                $recordArray = array();
                $recordArray["type"] = "Update";
                $recordArray["primarykey"] = Array($this->primaryKey[0],);
                $recordArray["tablename"] = $this->getTableName();
         
                $oldArray = $oriData->getDataArray();
                foreach($newArray as $key =>$newValue)
                {
                   if($newValue!=$oldArray[$key])
                   {
                       $recordArray["data"][$key]["old"] = $oldArray[$key];
                       $recordArray["data"][$key]["new"] = $newValue;
                   }
                }
                $this->dataDiff[] = $recordArray;
            }
            
        }
         return $result;
     }
     
     
     public function getDb()
     {
         return $this->db;
     }
     
     public function hasPrimaryKeyValue()
     {
        $result = false;
         $key= $this->primaryKey[0];
        $value = $this->get($key);

        if($value!=null&&trim($value)!=""&&!$this->isForceAdd())
        {
          
                 $result = true;
        }     
         
         return  $result;
     }

     public function getPrimaryKeyValue()
     {
        return $this->get($this->primaryKey[0]);
     }
  
     public function createUpdate($getSql=false,$getRecord=false,$setMainId=false)
     {
            $result = null;
             if($this->hasPrimaryKeyValue())
             {       
                       
                $result = $this->update($getSql,$getRecord);
             }
             else
             {           

                   $result = $this->create($getSql,$getRecord);
                   if($setMainId)
                   {

                      $this->set($this->primaryKey[0],$result);
                   }
             }
             return $result;
        
     }
     public function toXml()
     {
        $xml =  "\t<data tableName=\"".$this->getTableName()."\">\n";
        foreach ($this->getDataArray() as $key=>$value){
            $xml.="\t\t<".$key;
            if(in_array($key,$this->getPrimaryKey()))
            {
                $xml.=" pk=\"true\"";
            }
            else
            {
                $xml.=" pk=\"false\"";
            }
            if($value==null)
            {
                $value = "";
            }
            if(trim($value)==""){
                $xml.="></".$key.">\n";
            }
            else {
                $xml."><![CDATA[".trim($value).">\n";
            }
        }
        $xml.="\t</data>\n";
        return $xml;
     }
}


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
    $pdo = $db;
    $dataset = $pdo->openQuery($mutilSql); 

    $result = Array();
    $i = -1;
    do {
        $i++;
        $tableName = $tableArray[$i];
      
         if($getArray)
         {
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
         
            if($db->getResult()){
               
                  foreach($db->getResult() as $r)
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
       $sql = "SHOW TABLES LIKE '%" . $tablename . "%'";
       $tmp = $db->openQuery($sql);
       $result = false;
       if(count($result)==1)
        {
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
             "prefixmethod" => $prefixMethod,
             "isexoprt" => $isExoprt,
            
        );
        $this->$tranList = array_merge($this->$tranList,array($temp,));
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
?>