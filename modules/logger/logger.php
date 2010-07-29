<?php
/****************************************************************************
 * Copyleft lostpassword                                                    *
 * [gdb.lost@gmail.com]                                                     *
 *                                                                          *
 *                                                                          *
 * misTET is free software: you can redistribute it and/or modify           *
 * it under the terms of the GNU General Public License as published by     *
 * the Free Software Foundation, either version 3 of the License, or        *
 * (at your option) any later version.                                      *
 *                                                                          *
 * misTET is distributed in the hope that it will be useful,                *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of           *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
 * GNU General Public License for more details.                             *
 *                                                                          *
 * You should have received a copy of the GNU General Public License        *
 * along with misTET.  If not, see <http://www.gnu.org/licenses/>.          *
 ****************************************************************************/

define("__NAME__", "logger");
define("VERSION", "0.1.0");

session_start();

$config    = simplexml_load_file("resources/config.xml");
$protected = $config->protected == "true";
$file      = "resources/".$config->logFile;

if (!file_exists($file)) {
    $xml = new XMLWriter();
    $xml->openMemory();
    $xml->startDocument("1.0");
    $xml->writeElement("logs", "");
    $xml->endDocument();

    $fp = fopen($file, "w");
    fwrite($fp, "<?php die(\"Go away baby\");/*\n");
    fwrite($fp, $xml->outputMemory(true));
    fwrite($fp, "*/?>");
    fclose($fp);
}

if (!isset($_REQUEST["data"]) || !isset($_REQUEST["date"])) {
    if ($_SESSION["misTET"]["logged"]) {
        if (isset($_REQUEST["read"])) {
            header("Content-Type: text/xml");

            $content = file_get_contents($file);
            $content = explode("\n", $content);
            array_pop($content); array_shift($content);
            $content = implode("\n", $content);

            echo $content;
        }
    }
    else {
        echo "Go away baby";
    }

    exit;
}

$fp = fopen($file, "r+");

fseek($fp, -12, SEEK_END);

$xml = new XMLWriter();
$xml->openMemory();
$xml->startElement("log");

$xml->startElement("args");

$i = 0;
while ($_REQUEST[$i]) {
    $xml->startElement("argument");
    $xml->writeCData(str_replace("]]>", "]&#93;>", $_REQUEST[$i]));
    $xml->endElement();
    $i++;
}

$xml->endElement();

$xml->startElement("user");
    $xml->startElement("date"); 
		$xml->writeCData(str_replace("]]>", "]&#93;>", $_REQUEST["date"])); 
	$xml->endElement();
    $xml->startElement("ip"); 
		$xml->writeCData($_SERVER["REMOTE_ADDR"]); 
	$xml->endElement();
    $xml->startElement("agent"); 
		$xml->writeCData(str_replace("]]>", "]&#93;>", $_SERVER["HTTP_USER_AGENT"])); 
	$xml->endElement();
$xml->endElement();

$xml->startElement("referer"); $xml->writeCData(str_replace("]]>", "]&#93;>", $_SERVER["HTTP_REFERER"])); $xml->endElement();

$xml->endElement();

fwrite($fp, $xml->outputMemory(true));
fwrite($fp, "</logs>\n*/?>");
fclose($fp);

?>
