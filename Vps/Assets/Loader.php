<?php
class Vps_Assets_Loader
{
    static public function getAssetPath($url, $paths)
    {
        if (file_exists($url)) return $url;
        $type = substr($url, 0, strpos($url, '/'));
        $url = substr($url, strpos($url, '/')+1);
        if (!isset($paths->$type)) {
            throw new Vps_Assets_NotFoundException("Assets-Path-Type '$type' not found in config.");
        }
        $p = $paths->$type;
        if ($p == 'VPS_PATH') $p = VPS_PATH;
        if (!file_exists($p.'/'.$url)) {
            throw new Vps_Assets_NotFoundException("Asset-File '$p/$url' does not exist.");
        }
        return $p.'/'.$url;
    }

    static public function getFileContents($file, $paths)
    {
        $contents = file_get_contents(self::getAssetPath($file, $paths));
        if (substr($file, 0, 4)=='ext/') {
            //hack um bei ext-css-dateien korrekte pfade für die bilder zu haben
            $contents = str_replace('../images/', '/assets/ext/resources/images/', $contents);
        }

        if (substr($file, -4) == '.css') {
            static $cssConfig;
            if (!isset($cssConfig)) {
                try {
                    $cssConfig = new Zend_Config_Ini('application/config.ini', 'css');
                } catch (Zend_Config_Exception $e) {
                    $cssConfig = array();
                }
            }
            foreach ($cssConfig as $k=>$i) {
                $contents = preg_replace('#\\$'.preg_quote($k).'([^a-z0-9A-Z])#', "$i\\1", $contents);
            }
        }
        if (substr($file, -3) == '.js') {
            //TODO: wenn sowas öfters gebraucht wird dynamischer machen
            $hostParts = explode('.', $_SERVER['HTTP_HOST']);
            $configDomain = $hostParts[count($hostParts)-2]  // zB 'vivid-planet'
                            .$hostParts[count($hostParts)-1]; // zB 'com'
            if (isset(Zend_Registry::get('config')->googleMapsApiKeys->$configDomain)) {
                $contents = str_replace(
                    '{$googleMapsApiKey}',
                    Zend_Registry::get('config')->googleMapsApiKeys->$configDomain,
                    $contents
                );
            }
        }

        $version = Zend_Registry::get('config')->application->version;
        $contents = str_replace('{$application.version}', $version, $contents);
        $contents = self::trl($contents);
        $contents = self::hlp($contents);
        return $contents;
    }

    static public function load()
    {
        require_once 'Vps/Loader.php';
        Vps_Loader::registerAutoload();
        if (substr($_SERVER['REQUEST_URI'], 0, 8)=='/assets/') {

            $headers = apache_request_headers();

            if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
                $encoding = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
                            ? 'gzip' : (strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate')
                            ? 'deflate' : 'none');
            } else {
                $encoding = 'none';
            }

            $url = substr($_SERVER['REQUEST_URI'], 8);
            if (strpos($url, '?') !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
            if (preg_match('#^All([a-z]+)\\.(js|css)$#i', $url, $m)) {

                //falls der browser irgendwas im cache hat, hat sich das nie geändert
                //weil wenn wir eine neue version haben ändert sich die url

                //für offline die if außen rum, da ändert sich die version kaum
                //auch für auto-clear assets
                if (!(isset($_SERVER['SERVER_NAME']) && substr($_SERVER['SERVER_NAME'], -6) == '.vivid')) {
                    if (isset($headers['If-None-Match'])) {
                        header('HTTP/1.1 304 Not Modified');
                        header('ETag: '.$headers['If-None-Match']);
                        exit;
                    }
                    if (isset($headers['If-Modified-Since'])) {
                        header('HTTP/1.1 304 Not Modified');
                        header('Last-Modified: '.$headers['If-Modified-Since']);
                        exit;
                    }
                }

                if ($m[2] == 'js') {
                    header('Content-Type: text/javascript');
                    $fileType = 'js';
                } else {
                    header('Content-Type: text/css');
                    $fileType = 'css';
                }
                $section = $m[1];


                header('Last-Modified: '.gmdate("D, d M Y H:i:s \G\M\T", time()));
                header('ETag: abc-defg');

                $frontendOptions = array(
                    'lifetime' => null,
                    'automatic_serialization' => true
                );
                $backendOptions = array(
                    'cache_dir' => 'application/cache/assets/'
                );

                $sessionAssets = new Zend_Session_Namespace('debugAssets');
                $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
                $config = Zend_Registry::get('config');
                if ((!$cacheData = $cache->load($fileType.$encoding.$section))
                    || $cacheData['version'] != $config->application->version
                    || $sessionAssets->autoClearCache
                ) {
                    $dep = new Vps_Assets_Dependencies($section, $config);
                    $contents = $dep->getPackedAll($fileType);
                    $contents = self::_encode($contents, $encoding);
                    $cacheData = array('contents'=>$contents,
                                       'version'=>$config->application->version);
                    $cache->save($cacheData, $fileType.$encoding.$section);
                }
                header('Cache-Control: public');
                header("Content-Encoding: " . $encoding);
                echo $cacheData['contents'];
            } else {
                $config = Zend_Registry::get('config');
                $assetPath = self::getAssetPath($url, $config->path);
                if (!$assetPath) {
                    header("HTTP/1.0 404 Not Found");
                    die("file not found");
                }
                $http_if_modified_since = "";
                if (isset($headers['If-Modified-Since'])) {
                    $http_if_modified_since = preg_replace('/;.*$/', '', $headers['If-Modified-Since']);
                }
                $lastModified = max(filemtime('application/config.ini'), filemtime($assetPath));
                $lastModifiedString = gmdate("D, d M Y H:i:s \G\M\T", $lastModified);
                if ($http_if_modified_since == $lastModifiedString) {
                    header("HTTP/1.1 304 Not Modified");
                } else {
                    header('Last-Modified: '.$lastModifiedString);
                    header("Cache-Control: must-revalidate, max-age=".(24*60*60));
                    if (substr($url, -4)=='.gif') {
                        header('Content-Type: image/gif');
                    } else if (substr($url, -4)=='.png') {
                        header('Content-Type: image/png');
                    } else if (substr($url, -4)=='.jpg') {
                        header('Content-Type: image/jpeg');
                    } else if (substr($url, -4)=='.css') {
                        header('Content-Type: text/css');
                    } else if (substr($url, -3)=='.js') {
                        header('Content-Type: text/javascript');
                    } else if (substr($url, -4)=='.swf') {
                        header('Content-Type: application/flash');
                    } else if (substr($url, -4)=='.ico') {
                        header('Content-Type: image/x-icon');
                    } else {
                        header("HTTP/1.0 404 Not Found");
                        die("invalid file type");
                    }
                    $contents = self::getFileContents($url, $config->path);
                    header("Content-Encoding: " . $encoding);
                    echo self::_encode($contents, $encoding);
                }
            }
            exit;
        }
    }

    static private function _encode($contents, $encoding)
    {
        if ($encoding != 'none') {
            return gzencode($contents, 9, ($encoding=='gzip') ? FORCE_GZIP : FORCE_DEFLATE);
        } else {
            return $contents;
        }
    }

    static private function hlp($contents){
        $matches = array();
        preg_match_all("#hlp\('(.*)'\)#", $contents, $matches);
        foreach ($matches[0] as $key => $search) {
            $r = hlp($matches[1][$key]);
            $r = str_replace(array("\n", "\r", "'"), array('\n', '', "\\'"), $r);
            $contents = str_replace($search, "'" . $r . "'", $contents);
        }
        return $contents;
    }

    static private function trl ($contents){
        $type= '';
        preg_match_all('#trl'.$type.'\("(.+?)"\)|trl'.$type.'\(\'(.+?)\'\)#', $contents, $m);
        $expressions = self::_pregMatchTrl($m, '');
        $contents = self::_writeContent($m, $contents, $expressions);

        //das gleiche mit Vps
        preg_match_all('#trlVps\("(.+?)"\)|trlVps'.$type.'\(\'(.+?)\'\)#', $contents, $m);
        $expressions = self::_pregMatchTrl($m, 'Vps');
        $contents = self::_writeContent($m, $contents, $expressions);

        preg_match_all('#trl'.$type.'\(\'(.+?)\', (.*)\)|trl'.$type.'\(\"(.+?)\", (.*)\)#', $contents, $m);
        $expressions = self::_pregMatchTrl($m, '');
        $contents = self::_writeContent($m, $contents, $expressions);

        //das gleiche mit Vps
        preg_match_all('#trlVps\(\'(.+?)\', (.*)\)|trlVps'.$type.'\(\"(.+?)\", (.*)\)#', $contents, $m);
        $expressions = self::_pregMatchTrl($m, 'Vps');
        $contents = self::_writeContent($m, $contents, $expressions);

        preg_match_all('#trlc'.$type.'\(\'(.+?)\', +(.*), +(.*)\)|trlc'.$type.'\(\"(.+?)\", +(.*), +(.*)\)#', $contents, $m);
        $expressions = self::_pregMatchTrlc($m, '');
        $contents = self::_writeContent($m, $contents, $expressions);

        //das gleiche mit Vps
        preg_match_all('#trlcVps'.$type.'\(\'(.+?)\', +(.*), +(.*)\)|trlcVps'.$type.'\(\"(.+?)\", +(.*), +(.*)\)#', $contents, $m);
        $expressions = self::_pregMatchTrlc($m, 'Vps');
        $contents = self::_writeContent($m, $contents, $expressions);

        preg_match_all('#trlp'.$type.'\(\'(.+?)\', +(.*), +(.*)\)|trlp'.$type.'\(\"(.+?)\", +(.*), +(.*)\)#', $contents, $m);
        $expressions = self::_pregMatchTrlp($m, '');
        $contents = self::_writeContent($m, $contents, $expressions);

        //das gleiche mit Vps
        preg_match_all('#trlpVps'.$type.'\(\'(.+?)\', +(.*), +(.*)\)|trlpVps'.$type.'\(\"(.+?)\", +(.*), +(.*)\)#', $contents, $m);
        $expressions = self::_pregMatchTrlp($m, 'Vps');
        $contents = self::_writeContent($m, $contents, $expressions);

        preg_match_all('#trlcp'.$type.'\(\'(.+?)\', +(.*), +(.*), +(.*)\)|trlcp'.$type.'\(\"(.+?)\", +(.*), +(.*), +(.*)\)#', $contents, $m);
        $expressions = self::_pregMatchTrlcp($m, '');
        $contents = self::_writeContent($m, $contents, $expressions);

        //das gleiche mit Vps
        preg_match_all('#trlcpVps'.$type.'\(\'(.+?)\', +(.*), +(.*), +(.*)\)|trlcpVps'.$type.'\(\"(.+?)\", +(.*), +(.*), +(.*)\)#', $contents, $m);
        $expressions = self::_pregMatchTrlcp($m, 'Vps');
        $contents = self::_writeContent($m, $contents, $expressions);

        return $contents;
    }

    static private function _writeContent($m, $contents, $expressions) {
        if ($expressions) {
            foreach ($expressions as $values){
                $contents = str_replace($values['before'], $values['now'], $contents);
            }
        }
        return $contents;

    }

    static private function _pregMatchTrl ($m, $mode){

        $expressions = array();
        foreach($m[0] as $key => $trl){
            if ($m[1][$key] == ""){
                if (!($m[2][$key] == "")){
                    $values = array();
                    $values['before'] = $m[0][$key];
                    $values['tochange'] = $m[2][$key];
                    $method = "trl".$mode;
                    $values['now'] = $method(self::_getText($values['tochange']));
                    $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                    $values['now'] = str_replace($method, "trl", $values['now']);
                    $expressions[] =  $values;
                } else {
                    $values = array();
                    $values['before'] = $m[0][$key];
                    $values['tochange'] = $m[3][$key];
                    $method = "trl".$mode;
                    $values['now'] = $method(self::_getText($values['tochange']));
                    $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                    $values['now'] = str_replace($method, "trl", $values['now']);
                    $expressions[] =  $values;
                }
            } else {
                    $values = array();
                    $values['before'] = $m[0][$key];
                    $values['tochange'] = $m[1][$key];
                    $method = "trl".$mode;
                    $values['now'] = $method(self::_getText($values['tochange']));
                    $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);

                    $values['now'] = str_replace($method, "trl", $values['now']);
                    $expressions[] = $values;
            }
       }
       return $expressions;

    }

    static private function _pregMatchTrlc ($m, $mode){
        $expressions = array();
        foreach($m[0] as $key => $trl){
            if ($m[1][$key] == ""){
                if (!($m[2][$key] == "")){
                    $values = array();
                    $values['context'] = self::_getText($m[1][$key]);
                    $values['before'] = $m[0][$key];
                    $string = explode(',', $m[2][$key]);
                    $values['tochange'] = self::_getText($string[0]);
                    $method = "trlc".$mode;
                    $values['now'] = $method($values['context'] ,$values['tochange']);
                    $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                    $values['now'] = str_replace($method, 'trl', $values['now']);
                    $values['now'] = str_replace('\''.$values['context'].'\', ', '', $values['now']);
                    $expressions[] = $values;
                } else {
                    $values['context'] = self::_getText($m[4][$key]);
                    $values['before'] = $m[0][$key];
                    $string = explode(',', $m[5][$key]);
                    $values['tochange'] = self::_getText($string[0]);
                    $method = "trlc".$mode;
                    $values['now'] = $method($values['context'] ,$values['tochange']);
                    $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                    $values['now'] = str_replace($method, 'trl', $values['now']);
                    $values['now'] = str_replace("\"".$values['context']."\", ", '', $values['now']);
                    $expressions[] = $values;

                }
            } else {
                    $values = array();
                    $values['context'] = self::_getText($m[1][$key]);
                    $values['before'] = $m[0][$key];
                    $string = explode(',', $m[2][$key]);

                    $values['tochange'] = self::_getText($string[0]);
                    $method = "trlc".$mode;
                    $values['now'] = $method($values['context'] ,$values['tochange']);
                    $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                    $values['now'] = str_replace($method, 'trl', $values['now']);
                    $values['now'] = str_replace('\''.$values['context'].'\', ', '', $values['now']);
                    $expressions[] =  $values;
            }
        }
        return $expressions;
    }

    static private function _pregMatchTrlp ($m, $mode){
        $expressions = array();
        foreach($m[0] as $key => $trl){
            if ($m[1][$key] == ""){
                if (!($m[4][$key] == "")){

                $values = array();
                $values['before'] = $m[0][$key];
                $strings = self::_splitStringTrlp($values['before'], "\"", $mode);
                $values['single'] = $strings[0];
                $values['plural'] = $strings[1];
                $values['value'] = substr($strings[2], 0, 1);
                $values['tochange'] = $strings[1];

                //$method = 'trlp'.$mode;
                $method = 'getTrlpValues';
                $newValues = $method(null, $values['single'], $values['plural'], $mode);

                $method = 'trlp'.$mode;
                $values['now'] = str_replace($values['single'], $newValues['single'], $values['before']);
                $values['now'] = str_replace($values['plural'], $newValues['plural'], $values['now']);
                $values['now'] = str_replace($method, 'trlp', $values['now']);

                $expressions[] =  $values;
                }
            } else {

                $values = array();
                $values['before'] = $m[0][$key];
                $strings = self::_splitStringTrlp($values['before'], '\'', $mode);
                $values['single'] = $strings[0];
                $values['plural'] = $strings[1];
                $values['value'] = substr($strings[2], 0, 1);

                $values['tochange'] = $strings[1];
                $values['before'];
                $method = 'getTrlpValues';
                $newValues = $method(null, $values['single'], $values['plural'], $mode);

                $method = 'trlp'.$mode;
                $values['now'] = str_replace($values['single'], $newValues['single'], $values['before']);
                $values['now'] = str_replace($values['plural'], $newValues['plural'], $values['now']);
                $values['now'] = str_replace($method, 'trlp', $values['now']);

                $expressions[] = $values;
            }
        }
        return $expressions;
    }

    static private function _pregMatchTrlcp ($m, $mode){
        $expressions = array();
        foreach($m[0] as $key => $trl){
            if ($m[1][$key] == ""){
                $values = array();
                $values['before'] = $m[0][$key];
                $strings = self::_splitStringTrlcp($values['before'], "\"", $mode);
                $values['context'] = $strings[0];
                $values['single'] = $strings[1];
                $values['plural'] = $strings[2];
                $values['value'] = substr($strings[3], 0, 1);
                $values['tochange'] = $strings[2];

                $method = 'getTrlpValues';
                $newValues = $method($values['context'], $values['single'], $values['plural'], $mode);

                $method = 'trlcp'.$mode;
                $values['now'] = str_replace($values['single'], $newValues['single'], $values['before']);
                $values['now'] = str_replace($values['plural'], $newValues['plural'], $values['now']);
                $values['now'] = str_replace("\"".$values['context']."\",", "", $values['now']);
                $values['now'] = str_replace($method, 'trlp', $values['now']);
                $expressions[] =  $values;
            } else {
                $values = array();
                $values['before'] = $m[0][$key];
                $strings = self::_splitStringTrlcp($values['before'], '\'', $mode);
                $values['context'] = $strings[0];
                $values['single'] = $strings[1];
                $values['plural'] = $strings[2];
                $values['value'] = substr($strings[3], 0, 1); //derweil nur einstellig
                $values['tochange'] = $strings[2];

                $method = 'getTrlpValues';
                $newValues = $method($values['context'], $values['single'], $values['plural'], $mode);

                $method = 'trlcp'.$mode;
                $values['now'] = str_replace($values['single'], $newValues['single'], $values['before']);
                $values['now'] = str_replace($values['plural'], $newValues['plural'], $values['now']);
                $values['now'] = str_replace("\"".$values['context']."\",", "", $values['now']);
                $values['now'] = str_replace($method, 'trlp', $values['now']);


                $expressions[] =  $values;
            }
        }
        return $expressions;
    }



    static protected function _getText($name){
            if(strpos($name, '{')){
                $values = explode(',', $name);
                return str_replace("'", '', str_replace("\"", "" , $values[0]),str_replace("[", "" , str_replace("[", "" , $values[0])));
            } else {
                return str_replace("\"", "", str_replace("'", '', str_replace("[", "" , str_replace("]", "" , $name))));
            }
   }

   static protected function _splitStringTrlcp($string, $explode, $mode){
       $start = 0;
       $strings = explode($explode.',', $string);
       $strings[0] = str_replace('trlcp'.$mode.'(', '', self::_getText($strings[0]));
       $strings[++$start] = substr(self::_getText($strings[$start]), 1, strlen($strings[$start]));
       $strings[++$start] = substr(self::_getText($strings[$start]), 1, strlen($strings[$start]));
       $strings[++$start] = substr(str_replace('))', '', self::_getText($strings[$start])), 1, strlen($strings[$start]));
       return $strings;
   }

   static protected function _splitStringTrlp($string, $explode, $mode){
       $start = 0;
       $strings = explode($explode.',', $string);
       $strings[0] = str_replace('trlp'.$mode.'(', '', self::_getText($strings[0]));
       $strings[++$start] = substr(self::_getText($strings[$start]), 1, strlen($strings[$start]));
       $strings[++$start] = substr(str_replace('))', '', self::_getText($strings[$start])), 1, strlen($strings[$start]));
       return $strings;
   }
}
