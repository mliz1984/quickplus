<?php
 require_once(dirname(__FILE__) . "/quickFormDrawer.php");
class quickBuilder extends quickFormDrawer 
{

        public function getPageInfo($obj)
        { 
             $result = Array();
             $pageRows = $obj->getPageRows();
             $totalCount = $obj->getTotalCount();
             $resultSize = $obj->getResultSize();
             $totalPages = $obj->getTotalPages();
             $curPage = $obj->getCurPage();
             $startRecord = ($curPage-1)*$pageRows+1;
             $endRecord = $startRecord + $resultSize-1;
             $result["pageRows"] = $pageRows;
             $result["totalCount"] = $totalCount;
             $result["resultSize"] = $resultSize;
             $result["curPage"] = $curPage;
             $result["startRecord"] = $startRecord;
             $result["endRecord"] = $endRecord;
             return $result;
        }
        public function getTitleInfo($obj)
        {
            $result = Array();
            $titleinfo = $obj->getTitleInfo();
             foreach($titleinfo as $dbname =>$title)
             {
                 $structure = $obj->getStructureByDbName($dbname);
                 $method = "";
                 if($structure['ismethod'])
                 {
                      $method = $structure['methodname'];
                 }
                 $titleData = Array(
                                    "dbname" => $dbname,
                                    "title"=>$title["name"],
                                    "ordertype"=>$title['ordertype'],
                                    "width"=>$title['width'],
                                    "style"=>$structure['style'],
                                    "customMethod"=>$structure['ismethod'],
                                    "method" =>$method,
                                    );
                $result[$dbname] = $titleData;
             }
             return $result;
        }
        public function getSearchInfo($obj)
        {
             $result = Array();
             $searchField = $obj->getSearchField();
             foreach($searchField as $dbname => $methodname)
             {
                $displayname = $this->getDisplayName($dbname);
                $defaultsearch = $obj->getFormDefaultValue($dbname);
                $method = $fields[$dbname]["method"];
                if($quickFormDrawer->getSearchMode($dbname,$_REQUEST)!=null)
                {
                    $searchData = Array(
                                    "dbname" =>$dbname,
                                    "title"=>$displayname,
                                    "method"=>$method,
                                    "defaultsearch" =>$defaultsearch,
                                    );
                     $result[$dbname] = $searchData;
                }
             }
             return $result;
        }
        public function getDataInfo($obj)
        {
            return $obj->getResult();
        }
        public function getJsonString($db,$src,$curpage=1,$pagerows=null,$isExport=false,$blank=false)
        {
             $obj = $this->getForm($db,$src,$curpage,$pagerows,$isExport,$blank);
             $result = Array();
             $result["pageinfo"] = $this->getPageInfo($obj);
             $result["titleinfo"] = $this->getTitleInfo($obj);
             $result["searchinfo"] = $this->getSearchInfo($obj);
             $result["datainfo"]  = $this->getDataInfo($obj);
             return json_encode($result);
         }
}

?>