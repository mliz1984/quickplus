<?php
    namespace Quickplus\Lib;
 	class QuickTwilio extends QuickTwilioConfig
    {
        protected $client;
        protected $from; 
        public function __construct($from=null)
        {
              $this->client = new Services_Twilio($this->account_sid, $this->auth_token); 
              $this->from = $from;   
        }

        public function setFrom($from)
        {
            $this->from = $from;
        }

        public function getGmtDateTime($dataTime=null)
        {   if($dataTime==null)
            {
                   return date(DATE_RFC2822);
            }
            return date(DATE_RFC2822,strtotime($dataTime));
        }
        
        public function getDateTime($dataTime=null)
        {
             if($dataTime==null)
             {
                   return date("Y-m-d H:i:s");
             }
            return date("Y-m-d H:i:s",strtotime($dataTime));
        }

         public function sendSms($to,$msg,$media=null)
        {
          $from = $this->from;
          if($msg==null||trim($msg)=="")
          {
              return false;
          }
            $client = $this->client;
           $echo = null;
           try 
           {

            if(is_array($to))
            {
                $echo = Array();
                foreach($to as $number => $name)
                {
                    $tmp = $client->account->messages->sendMessage($from, $number, $msg,$media);
                }  
                $echo[] = $tmp;
            }
            else
            {
                $echo = $client->account->messages->sendMessage($from, $to, $msg,$media);
            }
            
           }
           catch(Exception $e)
           {
            echo 'Message: ' .$e->getMessage();
            return false;
           }
            $result =  json_decode($echo,true);
            
            return $result;
        }

        public function getMessages($to=null,$date=null)
        {
            $from = $this->from;
            $client = $this->client;
            $searchArr = Array();
            $isSearch = false;
            if($from!=null)
            {
               $isSearch = true; 
               $searchArr["From"] = $from;
            }
             if($to!=null)
            {
                $isSearch = true; 
                $searchArr["To"] = $to;
            }
              if($date!=null)
            {
                $isSearch = true; 
                $searchArr["DateSent"] = $date;
            }
            $messages = null;
            if($isSearch)
            {
                 $messages = $client->account->messages->getIterator(0, 50, $searchArr);  
       
            }
            else {
                  $messages = $client->account->messages->getIterator(0, 50); 
            }

           $result = Array();
           $last = null;
           foreach ($messages as $message) {
              
                $temp = Array();
                $temp["body"] = $message->body;
                $temp["to"] = $message->to;
                $temp["from"] = $message->from;
                $temp["sid"] = $message->sid;
                $temp["date_created"] = $message->date_sent;
                if($last!=null&&is_array($last))
                {
                     $time1 = strtotime($this->getDateTime($last["date_created"]));
                     $time2 = strtotime($this->getDateTime($temp["date_created"]));
                     $diff = abs($time2-$time1);
                    if($diff<=2 && $last["to"] ==  $temp["to"] && $last["from"] ==  $temp["from"])
                    {
                        $last["body"] =  $last["body"].$temp["body"];
                    }
                    else
                    {
                        $result[] = $last;
                        $last = $temp;
                    }
                }
                else
                {
                    $last = $temp;
                }
            }
             if($last!=null&&is_array($last))
             {
                 $result[] = $last;
             }
          
            return $result;
        }
    }
 ?>