<?php
namespace Quickplus\Lib;
   class DateUtil{
        public function getDayDiff($dataStr1,$dataStr2) 
        {
               $date1 =  $this->strToDate($dataStr1);
               $date2 =  $this->strToDate($dataStr2);
               return round(($date2-$date1)/3600/24); 
        }

        public function getHourDiff($dataStr1,$dataStr2)
        {
               $date1 =  $this->strToDate($dataStr1);
               $date2 =  $this->strToDate($dataStr2);
               return round(($date2-$date1)/3600);
        }

        public function getMinuteDiff($dataStr1,$dataStr2)
        {
               $date1 =  $this->strToDate($dataStr1);
               $date2 =  $this->strToDate($dataStr2);
               return round(($date2-$date1)/60);
        }


        public function getSecondDiff($dataStr1,$dataStr2)
        {
               $date1 =  $this->strToDate($dataStr1);
               $date2 =  $this->strToDate($dataStr2);
               return intval(($date2-$date1));
        }

        public function toTimeZone($src,$fm = 'Y-m-d H:i:s', $from_tz = 'Etc/UTC', $to_tz = 'America/Montreal') {
          $datetime = new DateTime($src, new DateTimeZone($from_tz));
          $datetime->setTimezone(new DateTimeZone($to_tz));
          return $datetime->format($fm);
        }

        public function getTimeZoneDiff($to_tz="Etc/UTC",$from_tz=null)
        {
           $now = date("Y-m-d H:i:s");
           $to = $this->toTimeZone($now,"Y-m-d H:i:s",date_default_timezone_get(),$to_tz);
           if($from_tz!=null)
           {
            
              $now = $this->toTimeZone($now,"Y-m-d H:i:s",date_default_timezone_get(),$from_tz);
              
           }
           return $this->getHourDiff($to,$now);
        }

        public function toUTC($src,$fm = 'Y-m-d H:i:s',$from_tz = 'America/Montreal')
        {
           return $this->toTimeZone($src,$fm,$from_tz,"Etc/UTC");
        }
        
         public function getMonthList($startMonth,$months,$reverse=false,$step=1)
         {
             $result = Array();
             $month = $startMonth;   
             $result[] = substr($month,0,7);
             $date = $month."-01";
             for($i=1;$i<$months;$i++)
             {
                $stepStr ="";
                $stepStr.= strval($step)."Months";
                if($step>0)
                {
                  $stepStr= "+".$stepStr;
                }
                
                 $month =  date("Y-m",strtotime($stepStr,strtotime($date)));
                 $result[] = $month;
                 $date = $month."-01";
             }  
             if($reverse)
             {
                $result = array_reverse($result);
             }
             return $result;
         }
         
         public function getDayList($startDay,$days,$forReport=false,$reportCol ="date",$reverse=false)
         {
             $result = Array();
             $day = $startDay;   
              if($forReport)
              {
                  $temp   = Array();  
                  $temp[$reportCol] = $day;
                  $result[] = $temp;
              }
              else 
              {
                  $result[] = $day;
              }
             for($i=1;$i<=$days;$i++)
             {
                 $day =  date("Y-m-d",strtotime("+".$i." day",strtotime($startDay)));
                 if($forReport)
                 {
                     $temp   = Array();  
                     $temp[$reportCol] = $day;
                     $result[] = $temp;
                 }
                 else 
                 {
                    $result[] = $day; 
                 }  
             }
             if($reverse)
             {
                $result = array_reverse($result);
             }
             return $result;
         }
         
         
         
        
         public function strToDate($dateStr) 
         {
              //echo $dateStr."%%%";
              $year=((int)substr($dateStr,0,4));
              $month=((int)substr($dateStr,5,2));
              $day=((int)substr($dateStr,8,2));
              $hour = 0;
              $minute = 0;
              $second = 0;
              if(strlen($dateStr)==19)
              {
                $hour = ((int)substr($dateStr,11,2));
                $minute = ((int)substr($dateStr,14,2));
                $second = ((int)substr($dateStr,17,2));
              }
              //echo $year."@".$month."@".$day;
              //echo "<br>".mktime(0,0,0,$month,$day,$year);
              return mktime($hour,$minute,$second,$month,$day,$year);
         }
         
         public function getMonthDiff($dataStr1,$dataStr2)
         {
              $dataStr1_year=intval(substr($dataStr1,0,4));  
              $dataStr1_month =intval(substr($dataStr1,5,2));  
              $dataStr2_year=intval(substr($dataStr2,0,4));  
              $dataStr2_month =intval(substr($dataStr2,5,2));
              $month =  ($dataStr2_year-$dataStr1_year)*12+$dataStr2_month-$dataStr1_month;
              return $month;
        }
         
        public function getYearDiff($dataStr1,$dataStr2)
        {
             $dataStr1_year=intval(substr($dataStr1,0,4));  
             $dataStr2_year=intval(substr($dataStr2,0,4));
             return $dataStr2_year - $dataStr1_year; 
        }
        
        public function getFirstDayInMonth($dataStr)
        {
            return substr($dataStr,0,7)."-01";
        }
        
        public function getLastDayInMonth($dataStr)
        {
             $year=substr($dataStr,0,4);  
             $month =substr($dataStr,5,2); 
             $days= cal_days_in_month(CAL_GREGORIAN, $month, $year);
             return $days;
        }
       
        public function getYearDays($dataStr)
        {
            $year=intval(substr($dataStr,0,4));
            $result = 365;
            if($year%4==0)
            {
                 $result = 366;
            }
            return $result;  
        }
        
        //returns the date given by startdate substracts diffInDay
        //$diffInDay should be the number of days, and could be both positive or negative.
        public function getDateFromDateWithDiff($startDate, $diffInDay, $dateFormat='Y-m-d')
        {
            $year=((int)substr($startDate,0,4));
            $month=((int)substr($startDate,5,2));
            $day=((int)substr($startDate,8,2));  
            $startDateInSec = mktime(0,0,0,$month,$day,$year);
            $diffInSec = $diffInDay * 24 * 60 * 60;
            return date($dateFormat, intval(substr($startDateInSec,0,10))+$diffInSec);
        }
       

        public function getDateFromTodayWithDiff($diffInDay, $dateFormat='Y-m-d')
        {
            $diffInSec = $diffInDay * 24 * 60 * 60;
            return date($dateFormat, time()+$diffInSec);
        }

   }


?>