<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 7:39 PM
 */

namespace Lib\Tools;


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