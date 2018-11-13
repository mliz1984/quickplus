<?php 
namespace Quickplus\Lib;
use Quickplus\Lib\DataMsg\Data;

	class QuickData extends Data{
		protected $linkMap = Array();
		protected $relationMap = Array();
		protected $dataMap = Array();
		protected $debug = false;
		protected $mainDataMsg = null;
        public function setDebug($debug)
        {
        	$this->debug = $debug;
        }
        
		protected function addLink($mark,$isSql,$value,$key,$linkKey,$linkMark=null,$db=null)
		{	
 				$array = Array(
 								"mark" => $mark,
 								"isSql" => $isSql,
 								"value" => $value,
 								"key" => $key,
 								"linkKey" => $linkKey,
 								"linkMark" => $linkMark,
 								"db" => $db,
 							   );
 				
 				$this->relationMap[$mark] = $linkMark;
 				$this->linkMap[$mark] = $array;
		
		}

		public function addData($mark,$data,$key,$linkKey,$linkMark=null)
		{
			$this->addLink($mark,false,$data,$key,$linkKey,$linkMark);
		}

		public function addSql($mark,$sql,$key,$linkKey,$linkMark=null,$db=null)
		{
			$this->addLink($mark,true,$sql,$key,$linkKey,$linkMark,$db);
		}
		protected function getDataMsgByWhere($mark,$where)
		{
					$sql = null;
					$db = $this->getDb();

					if($mark["isSql"])
					{
						$sql = "SELECT * FROM (".$mark["value"]." ) AS ".$mark." WHERE ".$where;
						if($mark["db"]!=null)
						{
							$db = $mark["db"];
						}
					}
					else
					{
						$data = $mark["value"];
						$db = $data->getDb();
						$data->setWhereClause($where);
						$sql = $data->find(0,1,true);
					}
					$dataMsg = new DataMsg();
					if($this->debug)
					{
						echo $sql."<br>";
					}
					$result = $dataMsg->findBySql($db,$sql);

					$this->dataMap[$mark["mark"]] = $result;
					return $result;
		}
		public function clear()
		{
			 $this->linkMap = Array();
			 $this->relationMap = Array();
			 $this->dataMap = Array();
			 $this->mainDataMsg = null;
		}
		public function getValue($key,$markId=null,$i=0)
		{
			$result = null;

			$dataMsg = $this->getDataMsg($markId);
			if($i<$dataMsg->getSize())
			{
				$data = $dataMsg->getData($i);
				$result = $data->get($key);
			}
			return $result;
		}
		
		protected function getMainDataMsg($pagerows=0,$curpage=1,$getSql=false)
		{
			if($this->mainDataMsg == null)
			{
				if($this->debug)
				{
					echo parent::find($pagerows,$curpage,true);
				}
				$this->mainDataMsg = $this->find($pagerows,$curpage,$getSql);

			}
			return $this->mainDataMsg;
		}
		public function getDataMsgSize($markId=null)
		{
			$dataMsg = $this->getDataMsg();
			return $dataMsg->getSize();
		}
		public function getDataMsg($markId=null)
		{	
		
			$result = null;
			if($markId==null)
			{
				$result =  $this->getMainDataMsg();
			}
			else
			{
				
				$result = $this->dataMap[$markId];
				if($result ==null)
				{

					$mark = $this->linkMap[$markId];
					$parentMarkId = $this->relationMap[$markId];
					$idStrs = null;
					if($parentMarkId==null)
					{
						$idStrs = $this->getDataMsg()->getIdStrs($mark["linkKey"]);
					}
					else
					{
						$parentData = $this->getDataMsg($parentMarkId);
						$parentMark = $this->linkMap[$parentMarkId];
						$idStrs = $parentData->getIdStrs($mark["linkKey"]);	

					}
					$where = $mark["key"]." in (".$idStrs.")";
					$result = $this->getDataMsgByWhere($mark,$where);
				}
			}
			return $result;
		}
	}
?>