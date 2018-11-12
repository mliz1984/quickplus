<?php
namespace Quickplus\Lib;
use Quickplus\Lib\QuickProxyConfig as QuickProxyConfig;
require_once(dirname(__FILE__) . "/commonTools.php");
class QuickProxy extends QuickProxyConfig
{
    protected $options = Array(
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_SSL_VERIFYPEER => false,
                    );
    protected $methodMapping = Array();
    public function setMethodMapping($id,$class=null,$src=null)
    {
        if($class==null)
        {
            $class = $id;
        }
        $this->methodMapping[$id]["src"] = $src;
        $this->methodMapping[$id]["class"] = $class;
    }
    public function setOptions($options)
    {
        $this->options = $options;

    }
    public function changeOptions($key,$value)
    {
        $this->options[$key] = $value;
    }

    public function run()
    {
        $ret = Array("result"=>false,"error"=>"Nothing has been running.");
        $vr = $this->verfiy();
        if(is_array($vr))
        {
            if(!$vr["result"])
            {
                $ret["error"] = "No Permission or Invaild Request.";
            }
            else
            {

                $className = $vr["class"];
               if(!is_array($this->methodMapping[$className]))
               {
                    $ret["error"] = "Coundn't found Class:'".$calssName."', Please check mapping setting";
               }
               else
               {
                    try{
                          $src = $this->methodMapping[$className]["src"];
                          $class = $this->methodMapping[$className]["class"];
                          $vdata = $vr["data"];
                          $method  = $vr["method"];
                          if($src!=null&&trim($src)!="")
                          {
                            require_once($src);
                          }

                          $runClass = new $class();
                          $result = $runClass->$method($vdata);

                          $ret["result"] = true;
                          $ret["error"] = "";
                          $ret["data"] = $result;

                        }
                     catch (Exception $e) {
                        $ret["error"] = "Runtime Error:". $e->getMessage();
                      }
                }
              }
        }

        return json_encode($ret);
    }







    public function verfiy()
    {
        $ret = Array("result"=>false);
        $tmp = Array();
        $header = array_change_key_case(getallheaders(),CASE_LOWER);
        if($this->dataStoreInHeader)
        {
            $tmp= $header;
        }
        else
        {
            $tmp = $_REQUEST;
        }
        $tmp = array_change_key_case($tmp,CASE_LOWER);
        $data = Array();
        if(is_array($tmp))
        {
            $data = CommonTools::getDataArray($tmp,$this->dataStoreMark);

            $key= $this->getFullKey($data);
            $okey = $header[strtolower($this->keyMark)];

            if($key==$okey)
            {
                $method = $header["method"];
                $class = $header["class"];
                $ret = Array("result"=>true,"data"=>$data,"method"=>$method,"class"=>$class);
            }
        }

        return $ret;
    }


    protected function getFullKey($data)
    {
         $key = time(gmdate("Y-m-d H:i:s"));
         if($this->tokenExpried&&$this->tokenExpriedTime>0)
         {
            $key = intval($key/$this->tokenExpriedTime);
         }
         if($this->seed!=null&&trim($this->seed)!="")
         {
                 $key.="_" .$this->seed;
         }
         $fullkey = sha1(json_encode($data)."@".$key);
         return $fullkey;
    }
    public function submit($url,$data,$class,$method,$customHeader=null)
    {
         $ch = curl_init();
         $options = $this->options;
         $options[CURLOPT_URL] = $url;
         if($this->isWebAuth&&$this->webAuthUserName!=null&&trim($this->webAuthUserName)!=""&&$this->webAuthPassword!=null&&trim($this->webAuthPassword)!="")
         {

            $options[CURLOPT_HTTPAUTH] =  CURLAUTH_BASIC;
            $options[CURLOPT_USERPWD] = $this->webAuthUserName.":".$this->webAuthPassword;
         }
         curl_setopt_array($ch, $options);
         $header = Array();

         if(is_array($customHeader))
         {
             foreach($customHeader as $k=>$v)
             {
                $header[] = $k.":".$v;
             }
         }
         if($this->dataStoreInHeader)
         {
            foreach($data as $k =>$v)
            {

                $header[] = $this->dataStoreMark.$k.":".$v;
            }
         }
         else
         {
            $post = Array();
            foreach($data as $k =>$v)
            {
                 $post[$this->dataStoreMark."_".$k] = $v;
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

         }
         $header[] = "method:".$method;
         $header[] = "class:".$class;
         $fullkey = $this->getFullKey($data);
         $header[] = $this->keyMark.":".$fullkey;
         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
         $rs = curl_exec($ch);
         curl_close($ch);
         return json_decode($rs,true);
    }


}