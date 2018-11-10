<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 7:34 PM
 */

namespace Lib\Tools;


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