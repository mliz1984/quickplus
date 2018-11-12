<?php 
    require_once(dirname(__FILE__)."/parameters.php");
    require_once(dirname(__FILE__)."/quickFormConfig.php");
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
  class XmlTools
  {
      public static function xmlToAssoc($xml)
      {
          $tree = null;
          while($xml->read())
          {
              switch ($xml->nodeType) 
              {
                  case XMLReader::END_ELEMENT: return $tree;
                  case XMLReader::ELEMENT:

                      $node = array('tag' => $xml->name, 'value' => $xml->isEmptyElement ? '' : self::xmlToAssoc($xml));
                      if($xml->hasAttributes)
                      {
                        while($xml->moveToNextAttribute())
                        {
                            $node['attributes'][$xml->name] = $xml->value;
                        }
                      }
                      $tree[] = $node;
                      break;
                  case XMLReader::TEXT:
                  case XMLReader::CDATA:
                      $tree .= $xml->value;
              }
          }
          return $tree;
      }
  }
  class FileTools
  {
    public static function detectFileEncode($fileName,$isAbsolutePath=true) 
    {
      
        if(!$isAbsolutePath)
        {
           $fileName =  FileTools::getRealPath($fileName);
        }
        $str = file_get_contents($fileName);
        return StringTools::getEncode($str);
        
    }

    public static function getClassFilePath($className,$withBasePath=false)
    {
         
         $test = new  ReflectionClass($className);
         $fileName = $test->getFileName();
         if($fileName&&!$withBasePath)
         {  
            $fileName = self::formatPath($fileName,"/");
            $basePath = self::formatPath(BASE_PATH,"/");
            $fileName = substr($fileName,strlen($basePath)-1);
         }
         return $fileName;
    }

    public static function connectPath($path1,$path2,$dirSep=null)
    {
      if($dirSep==null||trim($dirSep)=="")
      {
        $dirSep = quickFormConfig::$dirSep;
      }
      $path1 = str_replace("/",$dirSep,$path1);
      $path1 = str_replace("\\",$dirSep,$path1);
      $path2 = str_replace("/",$dirSep,$path2);
      $path2 = str_replace("\\",$dirSep,$path2);
      return rtrim($path1,$dirSep).$dirSep.ltrim($path2,$dirSep);
    }

    public static function formatPath($path,$dirSep=null)
    {
      if($dirSep==null||trim($dirSep)=="")
      {
         $dirSep = quickFormConfig::$dirSep;
      }
      $path = str_replace("/",$dirSep,$path);
      $path = str_replace("\\",$dirSep,$path);
      return $path;
    }
    
    public static function getRealPath($path)
    {
       return self::connectPath(BASE_PATH,$path);
    }

    public  static function addFileToZip($path, $zip,$basepath=null) 
    {  
          if(is_dir($path))
          {
            $handler = opendir($path); 
            while (($filename = readdir($handler)) !== false) 
            {
              if ($filename != "." && $filename != "..") 
              {
                  if (is_dir($path . "/" . $filename)) 
                  {
                      $zip = self::addFileToZip($path . "/" . $filename, $zip,$bashpath);
                  } 
                  else 
                  {
                      $fullpath = $path . "/" . $filename;
                      $filepath = $fullpath;
                      if($basepath!=null&&trim($basepath)!=""&&StringTools::isStartWith($filepath,$basepath))
                      {
                      		$filepath = ltrim(ltrim(substr($filepath,strlen($basepath)),"\\"),"/");
                      }
                      $zip->addFile($fullpath,$filepath);
                  }
             }
            }
            @closedir($path);
          }
          else
          {
              if(file_exists($path))
              {
               $zip->addFile($path);
              }
          }
          return $zip;
    }

    public static function listFiles($path,$includeChild=true)
    {
       $result = Array();
       if(is_file($path))
       {
          $result[] = $path;
       }
       else
       {
          $handler = opendir($path); 
          while (($filename = readdir($handler)) !== false) 
          {
            if($filename != "." && $filename != "..") 
            {
                if(is_dir($path . "/" . $filename)) 
                {
                   if($includeChild)
                   {
                      $result = array_merge($result,self::listFiles($path . "/" . $filename, $includeChild));
                   }
                    
                } 
                else 
                {
                    $result[] = $path . "/" .$filename;
                }
            }
          }
       }
       return $result;
    }

    public static function zip($zipName,$file,$basepath=null,$includeDir=false)
    {
       $files = Array(); 

       if($includeDir||!is_dir($file))
       {

          $files[] = $file;
       }
       else
       {

          $files = self::listFiles($file);
       }
       self::zipFiles($zipName,$files,$basepath);
    }

    public static function zipFiles($zipName,$files,$basepath=null)
    {
      $zip = new ZipArchive();
       if($zip->open($zipName, ZipArchive::OVERWRITE) !== TRUE) {
           touch($zipName);
       }
      if($zip->open($zipName, ZipArchive::OVERWRITE) === TRUE) {
          foreach($files as $f)
          {
            self::addFileToZip($f, $zip,$basepath);
          }
          $zip->close(); 
       }
    }
    
    public static function createDir($aimUrl,$right=0755,$isAbsolutePath=true) {
        if(!$isAbsolutePath)
        {
           $aimUrl =  FileTools::getRealPath($aimUrl);
        }

        $aimUrl = str_replace('', '/', $aimUrl);
        $aimDir = '';
        $arr = explode('/', $aimUrl);
        $result = true;
        foreach ($arr as $str) {
            $aimDir .= $str . '/';
       
            if (!file_exists($aimDir)) {
                $result = mkdir($aimDir, $right); 
                    chmod($aimDir, $right);

            }
        }
        return $result;
    }

   
    public static function createFile($aimUrl, $overWrite = false) {
        if (file_exists($aimUrl) && $overWrite == false) {
            return false;
        } elseif (file_exists($aimUrl) && $overWrite == true) {
            self::unlinkFile($aimUrl);
        }
        $aimDir = dirname($aimUrl);
        self::createDir($aimDir);
        touch($aimUrl);
        return true;
    }

    
    public static function moveDir($oldDir, $aimDir, $overWrite = false,$deleteDir=true) {
        $aimDir = str_replace('', '/', $aimDir);
        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
        $oldDir = str_replace('', '/', $oldDir);
        $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
        if (!is_dir($oldDir)) {
            return false;
        }
        if (!file_exists($aimDir)) {
            self::createDir($aimDir);
        }
        @ $dirHandle = opendir($oldDir);
        if (!$dirHandle) {
            return false;
        }
        while (false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!is_dir($oldDir . $file)) {
                self::moveFile($oldDir . $file, $aimDir . $file, $overWrite);
            } else {
               $result = self::moveDir($oldDir . $file, $aimDir . $file, $overWrite,$deleteDir);
            }
        }
        closedir($dirHandle);
        $result = true;
        if($deleteDir)
        {
         $result = rmdir($oldDir);
        }
    }

   public static function downloadFile($url,$save_dir='',$filename='',$isCurl=true){
          if($url==null||trim($url)==''){
             return false;
          }
          if($save_dir==null||trim($save_dir)==''){
             $save_dir='./';
          }
          if(0!==strrpos($save_dir,'/')){
             $save_dir.='/';
          }
          if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
             return false;
          }
         if($isCurl)
         {
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $content=curl_exec($ch);
            curl_close($ch);
         }
         else
         {
            ob_start();
            readfile($url);
            $content=ob_get_contents();
            ob_end_clean();
         }
         $size=strlen($content);
         $fp2=@fopen($save_dir.$filename,'a');
         fwrite($fp2,$content);
         fclose($fp2);
         unset($content,$url);
         return true;
      }


    
    public static function moveFile($fileUrl, $aimUrl, $overWrite = false) {
        if (!file_exists($fileUrl)) {
            return false;
        }
        if (file_exists($aimUrl) && $overWrite = false) {
            return false;
        } elseif (file_exists($aimUrl) && $overWrite = true) {
            self::unlinkFile($aimUrl);
        }
        $aimDir = dirname($aimUrl);
        self::createDir($aimDir);
        self::copyFile($fileUrl, $aimUrl,true);
        self::unlinkFile($fileUrl);
        return true;
    }

    
    public static function unlinkDir($aimDir,$includeSelf=true) {
        $aimDir = str_replace('', '/', $aimDir);
        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
        if (!is_dir($aimDir)) {
            return false;
        }
        $dirHandle = opendir($aimDir);

        while (false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!is_dir($aimDir . $file)) {
                self::unlinkFile($aimDir . $file);
            } else {
                self::unlinkDir($aimDir . $file);
            }
        }
        closedir($dirHandle);
        $result = true;
        if($includeSelf)
        {
          $result = rmdir($aimDir);
        }
        return $result;
    }

    
    public static function unlinkFile($aimUrl) {
        if (file_exists($aimUrl)) {
            unlink($aimUrl);
            return true;
        } else {
            return false;
        }
    }

    public static function getFileRight($filename)
    {
      $right =   intval(substr(base_convert(@fileperms($filename),10,8),-4),8); 
      return $right;
    }
    
    public static function copyDir($oldDir, $aimDir, $overWrite = false) {
        $aimDir = str_replace('', '/', $aimDir);
        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
        $oldDir = str_replace('', '/', $oldDir);
        $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
        if (!is_dir($oldDir)) {
            return false;
        }
        if (!file_exists($aimDir)) {
            $right = self::getFileRight($oldDir);
            self::createDir($aimDir,$right);
        }
        $dirHandle = opendir($oldDir);
        while (false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!is_dir($oldDir . $file)) {
                self::copyFile($oldDir . $file, $aimDir . $file, $overWrite);
            } else {
                self::copyDir($oldDir . $file, $aimDir . $file, $overWrite);
            }
        }
        return closedir($dirHandle);
    }

  
    public static function copyFile($fileUrl, $aimUrl, $overWrite = false) {
        if (!file_exists($fileUrl)) {
          
            return false;
        }
        if (file_exists($aimUrl) && $overWrite == false) {
            return false;
        } elseif (file_exists($aimUrl) && $overWrite == true) {

            self::unlinkFile($aimUrl);
        }
        $aimDir = dirname($aimUrl);
        self::createDir($aimDir);
        copy($fileUrl, $aimUrl);
        $right = self::getFileRight($fileUrl);
        chmod($aimUrl, $right);
        return true;
    }

    public static function getExtension($filename)
    {
       return strtolower(end(explode(".",$filename)));
    }
    public static function getFileList($path,$includeDir,$includeChildPath,$extensions=null,$spiltBy=null)
    {
        $checkExtension = false;
        if($extensions!=null)
        {
          if(is_string($extensions)&&trim($extensions)!="")
          {
              if($spiltBy!=null&&trim($spiltBy)!="")
              {
                $extensions = explode($spiltBy, $extensions);
              }
              else
              {
                $extensions = Array($extensions);
              }
          }
          if(count($extensions)>0)
          {
            $extensions = array_change_key_case($extensions,CASE_LOWER ); 
            $checkExtension = true;
          }
        }
         $files = array();        
          if(is_dir($path)) {        
              if($dh = opendir($path)) {        
                  while(($file = readdir($dh)) !== false) {        
                      if($file != '.' && $file != '..') { 
                         if(is_dir($file))
                         {
                            if($includeDir&&!$checkExtension)
                            {
                               $files[] = $file;
                            }
                            if($includeChildPath)
                            {
                               $tmp = $this->getFileList($file,$includeDir,$includeChildPath,$extensions,$spiltBy);
                               $files = array_merge($files,$tmp);
                            }
                         }
                         else
                         {
                           $add = true;
                           if($checkExtension)
                           {
                              $add = false;
                              $extension = self::getExtension($file);
                              if(in_array($extension, $extensions))
                              {
                                $add = true;
                              }
                           }
                           if($add)
                           {
                              $files[] = $file;
                           } 
                         }       
                      }        
                  }    
                  closedir($dh);        
              }        
          }        
          return $files;        
          
  }
}
	class StringTools
	{
    public static function parseExcelCellString($string)
    {
       $string = strtolower(trim($string));
       $result = null;
       $row = "";
       $col = "";
       if(strlen($string)>1&&ctype_lower(substr($string,0,1)))
       {
          $row = "";
          $col = "";
          for($i=0;$i<strlen($string);$i++)
          {
              $a = substr($string,$i,1);
              if(ctype_lower($a))
              {
                 $col .= $a;
              }
              else
              {
                 $row .= $a;
              }
          }
          $result = Array("row"=>$row,"col"=>$col);
       }
       return $result;

    }
    public static function escapeJsString($value)
    { 
        $escapers = array("'", "\"", "&");
        $replacements = array("\\'", "\\\"", "\\&");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }
     public static function escapeJsonString($value)
    { 
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }
    public static function getEncode($string)
    {
       return mb_detect_encoding($string, array('WINDOWS-1252','ASCII','GB2312','GBK','BIG5','UTF-8'));
    }
    public static function conv($string,$dst="UTF-8",$src=null)
    {
	  
      if($src==null)
      {
        $src = self::getEncode($string);
      }

	   if($src=="CP936"||$src=="ASCII"||$src="EUC-CN")
	   {
		    $src = QuickFormConfig::$encode;
	   }
 
     $string =  iconv($src,$dst,$string);
      return  $string;
    }
    public static function getRandStr($len=6,$format='ALL')
    {
      $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    switch($format){
      case 'ALL':
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      break;
      case 'CHAR':
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      break;
      case 'NUMBER':
        $chars='0123456789';
      break;
    }
    mt_srand((double)microtime()*1000000*getmypid()); 
    $result='';
    while(strlen($result)<$len){
      $result.=substr($chars,(mt_rand()%strlen($chars)),1);
    }
    return $result;
  }

		public static function isStartWith($string,$begin)
        {
         	$result = false;
         	$length = strlen($begin);
          $tmp = substr($string,0,$length);
            if($tmp==$begin)
            {
            	$result = true;
            }
            return $result;
        }

        public static function isEmpty($string)
        {
           $result = true;
           if(is_string($string)&&$string!=null&&trim($string)!="")
           {
             $result = false;
           }
           return $result;
        }

        public static function isEndWith($string,$end)
        {
        	$result = false;
         	$length = strlen($string)-strlen($end);
         	$tmp = substr($string,$length);
            if($tmp==$end)
            {
            	$result = true;
            }
            return $result;
        }

        public static function cutString($string,$start,$end,$with=true)
        {
            $result = null;
            if(strstr($string,$start)&&(strstr($string,$end)||!$end))
            {

               $startPoint = strpos($string,$start);
               $string  = substr($string,$startPoint+strlen($start));          
              $endPoint = true;
              if($end)
              {
                $endPoint = strpos($string,$end);
              }
               if($endPoint)
               {
                if($end)
                {
                 $result = substr($string,0,$endPoint);
                }
                else 
                {
                   $result = $string;
                }
                 if($with)
                 {
                    $result = $start.$result;  
                    if($end)
                    {
                      $result.=$end;
                    }
                 }
               }
            }
            return $result;
        }
        

	}
  class jqueryTools{
        public static function arrayToString($array)
        {
            $result = "";
            foreach($array as $key=>$value)
            {
               if(trim($result!=""))
               {
                 $result .=",";
               }
               if(!is_array($value))
               {
                 if(is_bool($value))
                 {
                    $tmp = "false";
                    if($value)
                    {
                      $tmp = "true";
                    }
                    $result .= $key.":".$tmp."";
                 }
                 else if(is_string($value)&&!StringTools::isStartWith(trim(strtolower($value)),"function")&&!StringTools::isStartWith(trim(strtolower($value)),"["))
                 {
                   $result .= $key.":'".$value."'";
                 }
                 else
                 {
                     $result .= $key.":".$value;
                 }
               }
               else
               {
                  $result .= $key.":{".jqueryTools::arrayToString($value)."}";
               }
            
            }
            return $result;
        }
  }
  class CommonTools{

        public static function toTimeZone($src, $from_tz = 'Asia/Shanghai', $to_tz = 'America/Montreal', $fm = 'Y-m-d H:i:s') {
                  $datetime = new DateTime($src, new DateTimeZone($from_tz));
                  $datetime->setTimezone(new DateTimeZone($to_tz));
                  return $datetime->format($fm);
        }
        public static function getDataArray($src,$prefix,$blank=true,$subkey=true)
        {
            $search = "/^".$prefix."\.*/";
            $result = array();
            foreach ($src as $key=>$value)
            {   
                if(preg_match($search,$key))
                {
                    if($subkey)
                    {
                      $key = substr($key,strlen($prefix));
                    }
                    if(is_string($value))
                    {
                      $value =trim($value);
                    }
                    if($blank==true)
                    {
                      $result[$key] = $value;
                    }
                    elseif(isset($value) && ((is_string($value)&&strlen($value)>0)||(is_array($value)&&count($value)>0)))
                    {

                        $result[$key] = $value;
                    }
                }
            }   
          return $result;
        }

      public static function processMapArray($array,$path="root")
      {
         $result = array();
         $temp = array();
         if(is_array($array)&&count($array)>0)
         {
          foreach($array as $key => $value)
          {
              if(!is_array($value))
              {
                 $temp[$key] = $value;
              }
              else
              { 
                 
                 $subresult = self::processMapArray($value,$path.".".$key);
                 $result = array_merge($result,$subresult);
              }
          }
        }
          $result[$path] = $temp;
          return $result;

      }
  }
  class RestTools
  {
      public static function post($url,$data)
      {
            $ch = curl_init();  
            curl_setopt($ch, CURLOPT_URL, $url);  
            curl_setopt($ch, CURLOPT_POST, 1);  
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
            curl_setopt($ch , CURLOPT_RETURNTRANSFER, true); 
            $result =  curl_exec($ch);  
            curl_close($ch);  
            return $result;
      }
  }


	class DbTools{
          public static function checkSql($db,$sql)
          {
              $result = false;
              if(DbTools::getColnames($sql,true))
              {
                 $checkSql = "SELECT * FROM (".$sql.") sql_check WHERE 1=2";
                $result = $db->execSql($checkSql);
              }
              return $result;
          }

          public static function getTableKeyFormCol($col)
          {
            
              $tmp = explode(".", $col);
              return $tmp[0];
          } 

          public static function getColNameFormCol($col)
          {
              $tmp = explode(".", $col);
              return $tmp[count($tmp)-1];
          }  

        public static function getCountSql($sql,$colname="",$countcol=null)
        {

           $sql = trim($sql);
           $sql = str_replace(PHP_EOL, ' ', $sql);   
           $sign = "FROM";
           $inpart = 0;
           if($col==null||trim($col)=="")
           {
              $col = "*";
           }
           $add = false;
           $countsql = "SELECT count(".$col.") ".$colname." ";
           $arr = explode(" ",$sql);
           $countArr = count($arr);
           $inpart = 0;
           for($i=0;$i<$countArr;$i++) {
                  $value = $arr[$i];
                  $inpart = $inpart + count(explode("(",$value)) -  count(explode(")",$value));
                  if($inpart == 0 && trim(strtoupper($value))=="DISTINCT")
                  {

                      $countsql = "SELECT count(".$col.") ".$colname." FROM (".$sql.") totalcountsql"; 
                       break;
                  }
                  if(!$add)
                  {
                     if(trim(strtoupper($value))==$sign&&$inpart == 0)
                     {
                       $add = true;
                     }
                  }
                  if($add)
                  {
                    if($inpart == 0 && trim(strtoupper($value)) == "GROUP" && ($i+1)<$countArr && trim(strtoupper($arr[$i+1]))=="BY")
                    {

                       $countsql = "SELECT count(".$col.") ".$colname." FROM (".$sql.") totalcountsql"; 
                       break;
                    }
                    else
                    {
                      if($inpart == 0 && strtoupper(trim($value))=="ORDER"&&strtoupper(trim($arr[$i+1]))=="BY")
                      {
                          break;
                      }
                      $countsql.=" ".trim($value);
                    }
                  }

           }  
        
           return $countsql;
        }
        

        public static function getOrderArray($sql)
        {
           $sql = trim($sql);
           $sql = str_replace(PHP_EOL, ' ', $sql);   
           $tmp = explode(" ",$sql);
           $result = Array();
           $result["select"] = $sql;
           $orderArr["order"] = null;
           $start = null;
           $arr = Array();
           foreach($tmp as $t)
           {
              if($t!=null&&trim($t)!="")
              {
                  $arr[] = $t;
              }
           }
           $length = count($arr);
           for($i=0;$i<$length;$i++)
           {
             if($i+1<$length&&strtoupper(trim($arr[$i]))=="ORDER"&&strtoupper(trim($arr[$i+1]))=="BY"&&($i>$start||$start==null))
              {
                $start = $i;
              }
           }
           if($start!=null)
           {
                $order = array_slice($arr,$start+2);
                $tmp = "";
                foreach($order as $a)
                {
                  $tmp.=" ".$a;
                }
                $tmp = trim($tmp);
                $tmp=explode(",",$tmp);

                if(count($tmp)>0)
                {

                  $result = Array();
                  $select =  implode(" ",array_slice($arr,0,$start));
                  $result["select"] = $select;
                  $orderArr["order"] = Array();
                  foreach($tmp as $c)
                  {
                    $ttmp = explode(" ",$c);
                    $t = Array();
                    foreach($ttmp as $ttt)
                    { 
                      if($ttt!=null&&trim($ttt)!="")
                      {
                          $t[] = $ttt;
                      }
                    }
                   
                    if(count($t)>0)
                    {
                       $a =  Array();
                       $tt = explode(".", $t[0]);
                       $a["col"] = $tt[count($tt)-1];
                       $a["fullcol"] = $t[0];
                       $type = "ASC";
                       if(count($t)>1)
                       {
                          for($j=1;$j<count($t);$j++)
                          {
                            if(strtoupper(trim($t[$j]))=="DESC")
                            {
                              $type = "DESC";
                            }
                          }
                       }
                       $a["type"] = $type;
                       $result['order'][] = $a;
                    }
                  }
                }
           }
           return $result;
        }
        
        public static function getColNames($sql,$check=false)
        {
           $checkResult = true;
           $voidArray = Array("DISTINCT",);
           $sql = trim($sql);
           $sql = str_replace(PHP_EOL, ' ', $sql);   
           $startSign = "SELECT";
           $stopSign = "FROM";
           $arr = explode(" ",$sql);
           $tmp = "";
           $add = false;
           $inpart = 0;
           foreach ($arr as $value) {

               $inpart = $inpart + count(explode("(",$value)) -  count(explode(")",$value));
               if(trim(strtoupper($value))==$stopSign&&$inpart == 0)
               {
                  break;
               }
               if($add)
               {
                   $value = trim($value);
                  if(!in_array($value, $voidArray))
                  {
                    $tmp.= " ".$value;
                  }
               }
               if(trim(strtoupper($value))==$startSign)
               {
                  $add = true;
              
               }
              
           }
           $temp = explode(",",$tmp);
           
           $arr = Array();
           $inpart = 0;
           $string = ""; 
           foreach($temp as $t)
           {
              if($string!="")
              {
                $string.=",";
              }
              $string .= $t;
              $inpart = $inpart + count(explode("(",$t)) -  count(explode(")",$t));
              if($inpart == 0)
              {
                 $arr[] = $string;
                 $string = "";
              }
              
           }  
    
         $result = Array();
         foreach ($arr as $value) {
              $value = trim($value);  
              if($value!=null&&$value!="")
              {
                  $k =""; 
                  $v ="";
                  $tmp = explode(" ",$value);
                  if(count($tmp)>1)
                  {
                    for($i=0;$i<count($tmp)-1;$i++)
                    {
                        $t = $tmp[$i];
                        if($v!="")
                        {
                           $v .= " ";
                        }
                        if(trim(strtoupper($t))=="AS")
                        {
                            break;
                        }  
                        $v.=$t;
                    }
                  }
                  else
                  {
                      $v = $tmp[0];
                  }
                 
                  if($tmp!=null&&trim($tmp[count($tmp)-1])!="")
                  {
                       $tmp =  trim($tmp[count($tmp)-1]);
                       $tmp = explode(".",$tmp);
                       if($tmp!=null&&trim($tmp[count($tmp)-1])!="")
                       {
                          $k = trim($tmp[count($tmp)-1]);
                       }
                  }
                  if(strtoupper(trim($k))!="TOP")
                  {
                     if($check&&isset($result[str_replace("`", "", $k)]))
                     {
                        $checkResult = false;
                     }
                     $k = str_replace("`", "", $k);
                     $k = str_replace("[", "", $k);
                     $k = str_replace("]", "", $k);
                     $v = str_replace("`", "", $v);
                     $result[$k] = $v;
                  }
                  
              }
         }
         if($check)
         {
           $result = $checkResult;
         }
         return $result; 
        }

        public static function getIdStrFromString($string,$sign=",")
        {
            $array = explode($sign, $string);
            return DbTools::getIdStrFromArray($array);
        }

        public static function getIdStrFromArray($array,$spilt=",",$marks="'")
        { 
            $result = "";
            foreach($array as $str)
            {
               if($str!=null&&trim($str)!="")
               {
                  $result.=$spilt.$marks.$str.$marks;
                }
            }
            return ltrim($result,$spilt);
        }
     }


     class ArrayTools{
       public static function getValues($array,$col,$spilt=",",$marks="'")
        {
          $result = "";
            foreach($array as $data)
            {
               $str = $data[$col];
               if($str!=null&&trim($str)!="")
               {
                  $result.=$spilt.$marks.$str.$marks;
                }
            }
            return ltrim($result,$spilt);

        }

        public static function getValueFromArray($array,$key,$defaultValue=null)
        { 
           $ret = $defaultValue;
           if(is_array($array)&&isset($array[$key]))
           {
              $ret = $array[$key];
           }
           return $ret;
        }

          public static function arrayRecursive($array, $function, $apply_to_keys_also = false,$jsonPre=false)
          {
              $result = array();
              foreach ($array as $key => $value) {
                  $newkey  = $key;
                  if ($apply_to_keys_also && is_string($key)) {
                  {
                      $newkey  = $function($key);
                      if($jsonPre)
                      {
                          $newkey = str_replace('"', '\"', $newkey);
                      }
                      
                  }
                  if (is_array($value)) {
                      $result[$newkey] = self::arrayRecursive($array[$key], $function, $apply_to_keys_also,$jsonPre);
                  } else {
                      if($jsonPre)
                      {
                          $value = str_replace('"', '\"', $value);
                        
                      }
                      $value = $function($value);
                      $result[$newkey] = $value;
                     
                  } 
                 
                }
              }
              return $result;
          }



           public static function jsonToArray($json,$function="urldecode",$apply_to_keys_also = true)
           {
              $json  = StringTools::conv($json);
              $array = json_decode($json);
              $array = self::arrayRecursive($array, $function, $apply_to_keys_also);
              return $array;
           }

           public static function arrayToJson($array,$function="urlencode",$apply_to_keys_also = true,$autoDecode=true) 
           {

              $array = self::arrayRecursive($array, $function, $apply_to_keys_also,true);
              
              $json = json_encode($array);
              if($autoDecode)
              {
                 $json = urldecode($json);
              }
              return $json;
           }

           


          public static function getWidth($array,$isKeyValueArray=true)
          {
              $width = 0;
              if($isKeyValueArray)
              {
                foreach($array as $key => $val)
                {
                   if(is_array($val))
                   {
                      $tmp += ArrayTools::getWidth($val,$isKeyValueArray);
                   }
                   if($tmp>$depth)
                   {
                      $width += 1;
                   }
                }
              }
              else
              {
                foreach($array as $val)
                {
                   if(is_array($val))
                   {
                      $tmp += ArrayTools::getWidth($val,$isKeyValueArray);
                   }
                   if($tmp>$depth)
                   {
                      $width += 1;
                   }
                }
              }
              return $width;
          }
          public static function getDepth($array,$isKeyValueArray=true,$count=0)
          {
              $depth = $count;
              if($isKeyValueArray)
              {
                foreach($array as $key => $val)
                {
                   $tmp = $count + 1 ;
                   if(is_array($val))
                   {
                      $tmp = ArrayTools::getDepth($val,$isKeyValueArray,$tmp);
                   }
                   if($tmp>$depth)
                   {
                      $depth = $tmp;
                   }
                }
              }
              else
              {
                foreach($array as  $val)
                {
                   $tmp = $count + 1 ;
                   if(is_array($val))
                   {
                      $tmp = ArrayTools::getDepth($val,$isKeyValueArray,$tmp);
                   }
                   if($tmp>$depth)
                   {
                      $depth = $tmp;
                   }
                }
              }
              return $depth;
          }

          public static function getMixMap($oriarray,$orikey,$addarray,$addkey,$mappingArray=null)
          {
             return ArrayTools::getMixArray($oriarray,$orikey,$addarray,$addkey,$mappingArray,true);
          }
          public static function getMixArray($oriarray,$orikey,$addarray,$addkey,$mappingArray=null,$isMixMap = false)
          {
             if($dstkey==null||trim($dstkey)!="")
             {
                $dstkey = $addkey;
             }
             $oriarray = ArrayTools::getKeyDataArray($oriarray,$orikey);
             $addarray = ArrayTools::getKeyDataArray($addarray,$addkey);
             $result = Array();
             foreach($oriarray as $key=>$data)
             {
                $orivalue = $data[$orikey];
                $addvalue = null;
                if(is_array($addarray[$key]))
                {
                     $addvalue = $addarray[$key][$addkey];
                      if($orivalue==$addvalue)
                      {
                         if($mappingArray==null)
                         {
                            foreach($addarray[$key] as $tkey =>$tvalue)
                            {
                                if($tkey!=$addkey)
                                {
                                   $data[$tkey] = $tvalue;
                                }

                            }
                         }
                         else
                         { 
                            foreach($mappingArray as $oldname => $newname)
                            {
                                $tmp =   $addarray[$key][$oldname];
                                $data[$newname] = $tmp;   
                            }
                         }
                      }
                }
                if($isMixMap)
                {
                  $result[$key] = $data;
                }
                else
                {
                  $result[] = $data;
                }
             }
             return $result;
          }
          public static function getKeyValueMap($array,$key,$value,$strval=false)
           {
                 $result = Array();
                 foreach($array as $data)
                 {
                     
                     $akey = $data[$key];
                     $avalue = $data[$value];
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

           public static function getKeyValueArray($array,$key,$value,$strval=false)
           {
                 $result = Array();
                 foreach($array as $data)
                 {
                     $akey = $data[$key];
                     $avalue = $data[$value];
                     if($strval)
                     {
                        $akey = strval($akey);
                        $avalue =  strval($avalue);
                     }
                     $result[$akey] = $avalue;
                 }
                 return $result;
            }
            public static function getKeyDataMap($array,$key,$strKey=false)
             {
                   $result = Array();
                  foreach($array as $data)
                   {
                       $akey = $data[$key];
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

            public static function getKeyDataArray($array,$key,$strKey=false,$case=2)
             {
                   $result = Array();
                   foreach($array as $data)
                   {

                       $akey = $data[$key];
                       if($strKey)
                       {
                          $aKey = strval($akey);
                       }
                          $temp = $data;
                          if($case==CASE_LOWER||$case==CASE_UPPER)
                          {
                           
                             $temp = array_change_key_case($temp,$case);
                          }
                          $result[$akey] = $temp;
                      
                   }
                   return $result;
             }

           public static function getCsvString($array,$oriCsv=false)
           {
              $result = "";
              foreach($array as $data)
              {
                  $str = "";
                  foreach($data as $key=>$value)
                  {
                       $value = str_replace("\"","\"\"",$value); 
                       if($oriCsv)
                        {
                            $str.= ",".$value;
                        }
                        else
                        {
                            $str.= ",\"".$value."\"";
                        }
                  }
                  $result.= substr($str, 1)."\n";
              }
              return $result;
           }
           
     }


     class ImgProcesser
     {

       /**
         * Text Watermark Point:
         *   #1      #2    #3
         *   #4   #5    #6
         *   #7      #8    #9
         */

        /**
         * ¸øÍ¼Æ¬Ìí¼ÓÎÄ×ÖË®Ó¡ ¿É¿ØÖÆÎ»ÖÃ£¬Ðý×ª£¬¶àÐÐÎÄ×Ö    **ÓÐÐ§×ÖÌåÎ´ÑéÖ¤**
         * @param string $imgurl  Í¼Æ¬µØÖ·
         * @param array $text   Ë®Ó¡ÎÄ×Ö£¨¶àÐÐÒÔ'|'·Ö¸î£©
         * @param int $fontSize ×ÖÌå´óÐ¡
         * @param type $color ×ÖÌåÑÕÉ«  Èç£º 255,255,255
         * @param int $point Ë®Ó¡Î»ÖÃ
         * @param type $font ×ÖÌå
         * @param int $angle Ðý×ª½Ç¶È  ÔÊÐíÖµ£º  0-90   270-360 ²»º¬
         * @param string $newimgurl  ÐÂÍ¼Æ¬µØÖ· Ä¬ÈÏÊ¹ÓÃºó×ºÃüÃûÍ¼Æ¬
         * @return boolean 
         */
        public static function createWordsWatermark($imgurl, $text, $fontSize = '14', $color = '0,0,0', $point = '1', $font = 'simhei.ttf', $angle = 0, $newimgurl = '') 
        {
            //echo $text;
            $text =  iconv("gb2312","utf-8",$text);
            //echo $text;
            $imageCreateFunArr = array('image/jpeg' => 'imagecreatefromjpeg', 'image/png' => 'imagecreatefrompng', 'image/gif' => 'imagecreatefromgif');
            $imageOutputFunArr = array('image/jpeg' => 'imagejpeg', 'image/png' => 'imagepng', 'image/gif' => 'imagegif');

          //echo "done";
            $imgsize = getimagesize($imgurl);

            if (empty($imgsize)) {
                return false; //not image
            }

            $imgWidth = $imgsize[0];
            $imgHeight = $imgsize[1];
            $imgMime = $imgsize['mime'];
            //echo $imgMime;
            if (!isset($imageCreateFunArr[$imgMime])) {
                return false; //do not have create img function
            }
            if (!isset($imageOutputFunArr[$imgMime])) {
                return false; //do not have output img function
            }

            $imageCreateFun = $imageCreateFunArr[$imgMime];
            $imageOutputFun = $imageOutputFunArr[$imgMime];

            $im = $imageCreateFun($imgurl);

            
            $color = explode(',', $color);
            $text_color = imagecolorallocate($im, intval($color[0]), intval($color[1]), intval($color[2])); 
            $point = intval($point) > 0 && intval($point) < 10 ? intval($point) : 1; 
            $fontSize = intval($fontSize) > 0 ? intval($fontSize) : 14;
            $angle = ($angle >= 0 && $angle < 90 || $angle > 270 && $angle < 360) ? $angle : 0; 
            $fontUrl = $font ? $font : 'simhei.ttf'; 
            $text = explode('|', $text);
            $newimgurl = $newimgurl ? $newimgurl : $imgurl; 

           
            $textLength = count($text) - 1;
            $maxtext = 0;
            foreach ($text as $val) {
                $maxtext = strlen($val) > strlen($maxtext) ? $val : $maxtext;
            }
            $textSize = imagettfbbox($fontSize, 0, $fontUrl, $maxtext);
            $textWidth = $textSize[2] - $textSize[1]; 
            $textHeight = $textSize[1] - $textSize[7]; 
            $lineHeight = $textHeight + 3; 
            if ($textWidth + 40 > $imgWidth || $lineHeight * $textLength + 40 > $imgHeight) {
                return false; 
            }

            if ($point == 1) { 
                $porintLeft = 20;
                $pointTop = 20;
            } elseif ($point == 2) { 
                $porintLeft = floor(($imgWidth - $textWidth) / 2);
                $pointTop = 20;
            } elseif ($point == 3) { 
                $porintLeft = $imgWidth - $textWidth - 20;
                $pointTop = 20;
            } elseif ($point == 4) { 
                $porintLeft = 20;
                $pointTop = floor(($imgHeight - $textLength * $lineHeight) / 2);
            } elseif ($point == 5) { 
                $porintLeft = floor(($imgWidth - $textWidth) / 2);
                $pointTop = floor(($imgHeight - $textLength * $lineHeight) / 2);
            } elseif ($point == 6) { 
                $porintLeft = $imgWidth - $textWidth - 20;
                $pointTop = floor(($imgHeight - $textLength * $lineHeight) / 2);
            } elseif ($point == 7) { 
                $porintLeft = 20;
                $pointTop = $imgHeight - $textLength * $lineHeight - 20;
            } elseif ($point == 8) { 
                $porintLeft = floor(($imgWidth - $textWidth) / 2);
                $pointTop = $imgHeight - $textLength * $lineHeight - 20;
            } elseif ($point == 9) { 
                $porintLeft = $imgWidth - $textWidth - 20;
                $pointTop = $imgHeight - $textLength * $lineHeight - 20;
            }

        
            if ($angle != 0) {
                if ($angle < 90) {
                    $diffTop = ceil(sin($angle * M_PI / 180) * $textWidth);

                    if (in_array($point, array(1, 2, 3))) {
                        $pointTop += $diffTop;
                    } elseif (in_array($point, array(4, 5, 6))) {
                        if ($textWidth > ceil($imgHeight / 2)) {
                            $pointTop += ceil(($textWidth - $imgHeight / 2) / 2);
                        }
                    }
                } elseif ($angle > 270) {
                    $diffTop = ceil(sin((360 - $angle) * M_PI / 180) * $textWidth);

                    if (in_array($point, array(7, 8, 9))) {
                        $pointTop -= $diffTop;
                    } elseif (in_array($point, array(4, 5, 6))) {
                        if ($textWidth > ceil($imgHeight / 2)) {
                            $pointTop = ceil(($imgHeight - $diffTop) / 2);
                        }
                    }
                }
            }

            foreach ($text as $key => $val) {
                 imagettftext($im, $fontSize, $angle, $porintLeft, $pointTop + $key * $lineHeight, $text_color, $fontUrl, $val);
            }

            if($imageOutputFun=='imagejpeg')
            {
              $imageOutputFun($im, $newimgurl, 80);
            }
            else
            {
              $imageOutputFun($im, $newimgurl);
            }
            //$imageOutputFun($im, $newimgurl, 80);

        imagedestroy($im);
            return $newimgurl;
        }


        public static function GetCreateFun($imgurl)
        {
            $imageCreateFunArr = array('image/jpeg' => 'imagecreatefromjpeg', 'image/png' => 'imagecreatefrompng', 'image/gif' => 'imagecreatefromgif');
           
            $imgsize = getimagesize($imgurl);

            if (empty($imgsize)) {
                return false; //not image
            }
            $imgMime = $imgsize['mime'];
            //echo $imgMime."@@@@";
            if (!isset($imageCreateFunArr[$imgMime])) {
                return false; //do not have create img function
            }

            $imageCreateFun = $imageCreateFunArr[$imgMime];
            return $imageCreateFun;
        }


        public static function GetOutputFun($imgurl)
        {
           $imageOutputFunArr = array('image/jpeg' => 'imagejpeg', 'image/png' => 'imagepng', 'image/gif' => 'imagegif');
           $imgsize = getimagesize($imgurl);

            if (empty($imgsize)) {
                return false; //not image
            }
            $imgMime = $imgsize['mime'];
            //echo $imgMime."!!!";
            //die();
            if (!isset($imageOutputFunArr[$imgMime])) {
              //echo $imgMime;
              //die();
                return false; //do not have create img function
            }

            $imageOutputFun = $imageOutputFunArr[$imgMime];
            return $imageOutputFun;
        }

        public static function resizeImage($src,$maxwidth,$maxheight,$name,$output=1)
        {
            $CreateFun = ImgProcesser:: GetCreateFun($src);
            $OutputFun = ImgProcesser:: GetOutputFun($src);
            $im = $CreateFun($src);

            $pic_width = imagesx($im);
            $pic_height = imagesy($im);

            if(($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight))
            {
                if($maxwidth && $pic_width>$maxwidth)
                {
                    $widthratio = $maxwidth/$pic_width;
                    $resizewidth_tag = true;
                }

                if($maxheight && $pic_height>$maxheight)
                {
                    $heightratio = $maxheight/$pic_height;
                    $resizeheight_tag = true;
                }

                if($resizewidth_tag && $resizeheight_tag)
                {
                    if($widthratio<$heightratio)
                        $ratio = $widthratio;
                    else
                        $ratio = $heightratio;
                }

                if($resizewidth_tag && !$resizeheight_tag)
                    $ratio = $widthratio;
                if($resizeheight_tag && !$resizewidth_tag)
                    $ratio = $heightratio;

                $newwidth = $pic_width * $ratio;
                $newheight = $pic_height * $ratio;

                if(function_exists("imagecopyresampled"))
                {
                    $newim = imagecreatetruecolor($newwidth,$newheight);
                   imagecopyresampled($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
                }
                else
                {
                    $newim = imagecreate($newwidth,$newheight);
                   imagecopyresized($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
                }
                if($output==0)
                {
                  return $newim;
                }
                else
                {
                  $name = $name.$filetype;
                  $OutputFun($newim,$name);
                  imagedestroy($newim);
                }
                
            }
            else
            {
                if($output==0)
                {
                  return $newim;
                }
                else
                {
                  $name = $name.$filetype;
                  $OutputFun($im,$name);
                }
                
            }

        }
}
?>