<?php 
    require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
class QuickSql
{
          protected $parser = null;
          protected $tableMapping = null;
          protected $topMark ="_quickSql_topMark";
          function __construct($sql)
          {
             $this->parser = new PhpMyAdmin\SqlParser\Parser($sql);  
             $this->tableMapping = $this->calcTableMapping();
          }

          public function getTableMapping()
          {
             $ret = $this->tableMapping;
             if($ret==null)
             {
                $this->tableMapping = $this->calcTableMapping();
                $ret = $this->tableMapping;
             }
             return $ret;
          }
          public function modifyOnClause($alias,$onClause,$replace=false)
          {
             $parser = $this->parser;
             if(is_array($this->tableMapping[$alias]))
             {
                $type =$this->tableMapping[$alias]["type"];
                if($type=="join"&&isset($parser->statements[0]))
                {
                     $statement = $parser->statements[0];
                     $levelArray = Array();
                     $mark = $alias;
                     $index = $this->tableMapping[$mark]["index"];
                     $levelArray[] = $index;
                     $mark = $this->tableMapping[$mark]["parent"];
                     while($mark!=$this->topMark)
                     {      
                          $index = $this->tableMapping[$mark]["index"];
                          $levelArray[] = $index;  
                          $mark = $this->tableMapping[$mark]["parent"];
                            
                     }
                     $levelNum = count($levelArray);

                     if($levelNum==1)
                     {   
                        if(!$replace)
                        {

                          $onClause =  $statement->join[$index]->on[0]->expr." ".$onClause;
                        }
                        $condition = new PhpMyAdmin\SqlParser\Components\Condition($onClause);
                        $index = $levelArray[0];
                        $statement->join[$index]->on = Array($condition);
                        $this->parser =  new PhpMyAdmin\SqlParser\Parser($statement->build()); 
                       
                     }
                     else if($levelNum>1)
                     {
                        $objArray = Array();
                        $obj = null;

                        for($i=$levelNum;$i>0;$i--)
                        {
                          
                           $index = $levelArray[$i-1];
                           if($i==$levelNum)
                           {
                              $obj = $statement->join[$index]->expr;
                              
                              $objArray[]=  $statement;
                           }
                           else
                           {
                              
                              $obj =  new PhpMyAdmin\SqlParser\Parser($obj->expr);
                              if(isset($obj->statements[0]))
                              {

                                $objArray[]=  $obj->statements[0];
                                $obj = $obj->statements[0]->join[$index]->expr; 
                              }

                           }
                        }

                        $length = count($objArray);
                        $condition = null;
                        for($i=$length;$i>0;$i--) 
                        {
                           $obj = $objArray[$i-1];
                           $index = $levelArray[$length-$i];
                           
                           if($i==$length)
                           {
                              if(!$replace)
                              {
                                $onClause =  $obj->join[$index]->on[0]->expr." ".$onClause;
                              }
                              $condition = new PhpMyAdmin\SqlParser\Components\Condition($onClause);
                          
                              $obj->join[$index]->on= Array($condition);
                              $onClause = $obj->build();

                           }
                           else
                           {
                              $obj->join[$index]->expr = "(".$onClause.")";
                              $onClause =  $obj->build();
                              if($i==1)
                              {
                                 $this->parser =  new PhpMyAdmin\SqlParser\Parser($onClause);     
                              }
                           }

                        }

                    
                     }
                  }
                     

                }
                


          }
          public function getSql()
          {
            $ret = "";
            $parser = $this->parser;
            if(isset($parser->statements[0]))
            {
              $statement = $parser->statements[0];
              $ret =  $statement->build();
            }
            return $ret;
          }

          public function calcTableMapping($sql=null,$topMark=null
          )
          {
            $array = Array();
            if($topMark==null)
            {
               $topMark = $this->topMark;
            }
            $parser = $this->parser;
            if($sql!=null)
            {
              $parser =  new PhpMyAdmin\SqlParser\Parser($sql);  
            }
            if(isset($parser->statements[0]))
            {
           
              $statement = $parser->statements[0];
              if(isset($statement->from))
              {
                foreach($statement->from as $i => $f)
                {
                   $alias = $f->alias;
                   if($alias==null&&trim($alias)=="")
                   {
                      $alias = $f->expr;
                   }
                    $alias = trim($alias,"`");
                   $array[$alias]["type"] = "from";
                   $array[$alias]["index"] = $i;
                   $array[$alias]["table"] = trim($f->expr,"`");
                   $array[$alias]["parent"] = $topMark;
                  if(isset($f->subquery)&&$f->subquery!=null&&trim($f->subquery)!="")
                  {
                     $subArray = $this->calcTableMapping($f->expr,$alias);
                     $array = array_merge($array,$subArray);
                  }
                }
              }
              if(isset($statement->join))
              {

                foreach($statement->join as $i => $j)
                {

                   $f = $j->expr;
                   $alias = $f->alias;
                   if($alias==null&&trim($alias)=="")
                   {
                      $alias = $f->expr;
                   }
                   $alias = trim($alias,"`");
                   $array[$alias]["table"] = trim($f->expr,"`");
                    $array[$alias]["type"] = "join";
                   $array[$alias]["index"] =$i;
                   $array[$alias]["parent"] = $topMark;
                   if(isset($f->subquery)&&$f->subquery!=null&&trim($f->subquery)!="")
                  {
                     $subArray = $this->calcTableMapping($f->expr,$alias);
                     $array = array_merge($array,$subArray);
                  }
                }
              }

            }
            return $array;
          }
}