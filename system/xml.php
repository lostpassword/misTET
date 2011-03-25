<?php
/**************************************************************************** 
 * Copyleft lostpassword                                                    * 
 * [gdb.lost@gmail.com]                                                     *
 *                                                                          *
 * This file is part of misTET.                                             *
 *                                                                          *
 * misTET is free software: you can redistribute it and/or modify           *
 * it under the terms of the GNU Affero General Public License as           *
 * published by the Free Software Foundation, either version 3 of the       *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * misTET is distributed in the hope that it will be useful,                *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of           *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
 * GNU Affero General Public License for more details.                      *
 *                                                                          *
 * You should have received a copy of the GNU Affero General Public License *
 * along with misTET.  If not, see <http://www.gnu.org/licenses/>.          *
 ****************************************************************************/

class XMLparser
{
	
    public function init ($file) {

        if (!file_exists($file)) {
            die (new Error("ERROR_XML", "{$file} doesn't exist"));
        }

        if (!is_readable($file)) {
            die (new Error("ERROR_XML", "{$file} has no +r perms"));
        }
		
        $xml = DOMDocument::load($file)->documentElement;
        
	$arr = $this->toArray($xml);
		
	$res = array(
	    "home" => (string) $arr["init"]["homePage"],
	    "title" => (string) $arr["init"]["title"],
	    "keywords" => (string) $arr["init"]["keywords"],
	    "description" => (string) $arr["init"]["description"]
	);
		
	return $res;
		
    }

    public function menu ($file) {

        $arr = array ();

        if (!file_exists($file) || !is_readable($file)) {
            die (new Error("ERROR_XML_MENU", "{$file} is missing or has wrong perms"));
        }

        $xml = DOMDocument::load($file)->documentElement;

        foreach ($xml->childNodes as $node) {
            if ($node->nodeName == 'menu') {
		$href = preg_replace('/#/', '?', $node->getAttribute('href'));

		preg_match('/?/', $href, $matches);
		if ($matches) {
                    $arr[$href] = $node->nodeValue;
		} else {
		    $arr["?".$href] = $node->nodeValue;
		}
            }
        }
        
        return $arr;
    }
    
    public function toArray($xml = null) {
        
        if (is_null($xml)) {
            return array();
        }

        $xml = (is_null($xml)) ? $xml->documentElement : $xml;

        if (!$xml->hasChildNodes()) {
            $result = $xml->nodeValue;

        } else {
            $result = array();

            foreach ($xml->childNodes as $node) {
                
                $nodeList = $xml->getElementsByTagName($node->nodeName); 
                $count = 0;
                
                foreach ($nodeList as $node2) {
                    if ($node2->parentNode->isSameNode($node->parentNode)) {
                        $count++;
                    }
                }

                $value = $this->toArray($node);
                $key   = ($node->nodeName{0} == '#') ? 0 : $node->nodeName;
                $value = is_array($value) ? $value[$node->nodeName] : $value;
                
                if ($count > 1) { 
                    $result[$key][] = $value;
                } else {
                    $result[$key] = $value;
                }
            }
            
            if (count($result) == 1 && isset($result[0]) && !is_array($result[0])) {
                $result = $result[0];
            }
        }
        
        $attributes = array();

        if ($xml->hasAttributes()) {

            foreach ($xml->attributes as $key=>$name) {
                $attributes["@{$name->nodeName}"] = $name->nodeValue;
            }
        }

        if (count($attributes)) {
            if (!is_array($result)) {
                $result = (trim($result)) ? array($result) : array();
            }
            $result = array_merge($result, $attributes);
        }

        $arResult = array($xml->nodeName=>$result);
        return $arResult;
    }
	 
}
 
?>