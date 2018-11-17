<?php
     namespace Quickplus\Lib;
     set_time_limit(0);
     use Quickplus\Lib\DataMsg\DataMsg;

class reportSelector{
      private $reportsOrder;
      private $reports;
      public function clear()
      {
          $this->reportsOrder=null;
          $this->reports=null;
      }
      public function addReport($reportName,$reporeObj,$changeReportName=true)
      {
              
          if($changeReportName)
          {
              $reporeObj->setReportName($reportName);
          }
          if(empty($this->reportsOrder))
          {
              $this->reportsOrder = array();
          }
          if(empty($this->reports))
          {
              $this->reports = array();
          }
          if(empty($this->reportsOrder))
          {
             $this->reportsOrder = array();
          }
          $this->reports[$reportName] = $reporeObj;
          
          if($this->getOrderByName($reportName)==null)
          {
              $this->reportsOrder = array_merge($this->reportsOrder,array($reportName));
              
          }
      }
      public function checkReportsOrder()
      {
         if($this->reportsOrder==null)
         {
             $this->reportsOrder = array();
         }
      }
      public function checkReports()
      {
         if($this->reports==null)
         {
                 $this->reports = array();
         }
      }
      
      
      public function getOrderByName($reportName)
      {
   
          $this->checkReportsOrder();
          for($i=0;$i<count($this->reportsOrder);$i++)
          {
              if($this->reportsOrder[$i]==$reportName)
              { 
                  return $i;
              }
          }
          return null;
      }
      public function getNameByOrder($i)
      {
          $this->checkReportsOrder();
          return $this->reportsOrder[$i];
      }
      public function getReportByName($reportName)
      {
          if($reportName==null)
          {
              return null;
          }
           $this->checkReports();
           return $this->reports[$reportName];
      }
      public function getReportByOrder($i)
      {
          return $this->getReportByName($this-> getNameByOrder($i));
      }
      
      public function getReportsSize()
      {
          if($this->reportsOrder==null)
         {
             return 0;
         }    
         return count($this->reportsOrder);
          
      }
    
}