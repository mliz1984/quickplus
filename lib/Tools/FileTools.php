<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 7:35 PM
 */

namespace Quickplus\Lib\Tools;


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
                        $zip = self::addFileToZip($path . "/" . $filename, $zip,$basepath);
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
                                $tmp = self::getFileList($file,$includeDir,$includeChildPath,$extensions,$spiltBy);
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
?>