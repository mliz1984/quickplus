<?php
namespace Quickplus\Lib;
require_once($_SERVER['DOCUMENT_ROOT']."/lib/PHPExcel.php");
$excel=\PHPExcel_IOFactory::load("file.xlsx");//
$sheet=$excel->getSheet(0);//
$data=$sheet->toArray();
$imgData=array();
$p_w_picpathFilePath='d:/test/';//图片保存目录
foreach($sheet->getDrawingCollection() as $img){
    list ($startColumn, $startRow) = \PHPExcel_Cell::coordinateFromString($img->getCoordinates());//获取列与行号
    $p_w_picpathFileName=$img->getCoordinates().mt_rand(100,999);
    $p_w_picpathFileName.=".".$img->getExtension();
    $filename = $img->getPath();  //文件
    copy($filename, $p_w_picpathFileName); 
         
    
    $imgData[$startRow][$startColumn]=$p_w_picpathFileName;
    print_r($imgData);
}
?>