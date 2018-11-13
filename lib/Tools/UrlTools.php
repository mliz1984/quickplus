<?php
namespace Quickplus\Lib\Tools;
use Quickplus\Lib\QuickFromConfig as QuickFormConfig;
use Quickplus\Lib\Parameters;

class UrlTools
{

    public function getParameterString($oriurl=null,$withQm=true)
    {
        if($oriurl==null&&trim($oriurl)=="")
        {
            $oriurl = $_SERVER['REQUEST_URI'];
        }
        $result =  trim(strstr($oriurl,"?"));
        if($result)
        {
            if(!$withQm)
            {
                $result = ltrim($result,"?");
            }
        }
        else
        {
            $result  ="";
        }
        return $result;
    }

    public static function getFullUrl($oriurl=null,$isAbsolutePath=false)
    {

        if($oriurl==null&&trim($oriurl)=="")
        {
            $oriurl = $_SERVER['REQUEST_URI'];

        }
        else
        {
            if($isAbsolutePath)
            {
                $oriurl = "/".substr($oriurl,strlen(BASE_PATH));
            }
        }
        $url='http://';
        if(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on'){
            $url='https://';
        }
        if($_SERVER['SERVER_PORT']!='80'){
            $url.=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
        }else{
            $url.=$_SERVER['SERVER_NAME'];
        }
        $oriurl = str_replace("\\","/",$oriurl);


        $url .=$oriurl;
        return $url;
    }
    public static function getPageUrl($pageUrl)
    {
        if($pageUrl==null)
        {
            $pageUrl = $_SERVER['REQUEST_URI'];
        }
        $pos = stripos($pageUrl,"?");
        if($pos)
        {
            $pageUrl = substr($pageUrl,0,$pos);
        }
        return $pageUrl;
    }
    public static function getSubmitMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }
}





?>