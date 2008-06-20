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
        if (!isset($_SERVER['REQUEST_URI'])) return;

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

                //für offline die if außen rum, da ändert sich die version kaum, die files schon
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
                    header('Content-Type: text/javascript; charset=utf-8');
                    $fileType = 'js';
                } else {
                    header('Content-Type: text/css; charset=utf-8');
                    $fileType = 'css';
                }
                $section = $m[1];


                header('Last-Modified: '.gmdate("D, d M Y H:i:s \G\M\T", time()));
                header('ETag: abc-defg');
                header('Cache-Control: public');
                header("Content-Encoding: " . $encoding);

                if ($section == 'RteStyles') {
                    $contents = Vpc_Basic_Text_StylesModel::getStylesContents();
                    echo self::_encode($contents, $encoding);
                } else {
                    $frontendOptions = array(
                        'lifetime' => null,
                        'automatic_serialization' => true
                    );
                    $backendOptions = array(
                        'cache_dir' => 'application/cache/assets/'
                    );
                    $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

                    $sessionAssets = new Zend_Session_Namespace('debugAssets');
                    $config = Zend_Registry::get('config');
                    if ((!$cacheData = $cache->load($fileType.$encoding.$section))
                        || $cacheData['version'] != $config->application->version
                        || $sessionAssets->autoClearCache
                        || $config->debug->autoClearAssetsCache
                    ) {
                        $dep = new Vps_Assets_Dependencies($section, $config);
                        $contents = $dep->getPackedAll($fileType);
                        $contents = self::_encode($contents, $encoding);
                        $cacheData = array('contents'=>$contents,
                                        'version'=>$config->application->version);
                        $cache->save($cacheData, $fileType.$encoding.$section);
                    }
                    echo $cacheData['contents'];
                }
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
                //NED EINCHCKEN!°!!!!!!!!
                if (false && $http_if_modified_since == $lastModifiedString) {
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
                        header('Content-Type: text/css; charset=utf-8');
                    } else if (substr($url, -3)=='.js') {
                        //NED EINCHCKEN!°!!!!!!!!header('Content-Type: text/javascript; charset=utf-8');
                    } else if (substr($url, -4)=='.swf') {
                        header('Content-Type: application/flash');
                    } else if (substr($url, -4)=='.ico') {
                        header('Content-Type: image/x-icon');
                    } else if (substr($url, -5)=='.html') {
                        header('Content-Type: text/html; charset=utf-8');
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

    static private function hlp($contents)
    {
        $matches = array();
        preg_match_all("#hlp\('(.*)'\)#", $contents, $matches);
        foreach ($matches[0] as $key => $search) {
            $r = hlp($matches[1][$key]);
            $r = str_replace(array("\n", "\r", "'"), array('\n', '', "\\'"), $r);
            $contents = str_replace($search, "'" . $r . "'", $contents);
        }
        return $contents;
    }

    static private function trl($contents)
    {
        foreach (Zend_Registry::get('trl')->parse($contents) as $trlelement) {
            $values = array();

            if ($trlelement['source'] == Vps_Trl::SOURCE_VPS) {
                $mode = "Vps";
            } else  {
                $mode = '';
            }

            if ($trlelement['type'] == 'trl') {
                $values['before'] = $trlelement['before'];
                $values['tochange'] = $trlelement['text'];
                $method = $trlelement['type'].$mode;
                $values['now'] = $method($values['tochange']);
                $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                $values['now'] = str_replace($method, "trl", $values['now']);

            } else if ($trlelement['type'] == 'trlc') {
                $values = array();
                $values['context'] = $trlelement['context'];
                $values['before'] = $trlelement['before'];
                $values['tochange'] = $trlelement['text'];
                $method = $trlelement['type'].$mode;
                $values['now'] = $method($values['context'] ,$values['tochange']);
                $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                $values['now'] = str_replace($method, 'trl', $values['now']);
                $values['now'] = str_replace('\''.$values['context'].'\', ', '', $values['now']);
                $values['now'] = str_replace('"'.$values['context'].'", ', '', $values['now']);

            } else if ($trlelement['type'] == 'trlp') {
                $values['before'] = $trlelement['before'];
                $values['single'] = $trlelement['text'];
                $values['plural'] = $trlelement['plural'];

                $newValues = Zend_Registry::get('trl')->getTrlpValues(null, $values['single'],
                                            $values['plural'], $trlelement['source'] );

                $method = $trlelement['type'].$mode;
                $values['now'] = str_replace($values['single'], $newValues['single'], $values['before']);
                $values['now'] = str_replace($values['plural'], $newValues['plural'], $values['now']);
                $values['now'] = str_replace($method, 'trlp', $values['now']);

            } else if ($trlelement['type'] == 'trlcp') {
                $values = array();
                $values['before'] = $trlelement['before'];
                $values['context'] = $trlelement['context'];
                $values['single'] = $trlelement['text'];
                $values['plural'] = $trlelement['plural'];

                $newValues = Zend_Registry::get('trl')->getTrlpValues($values['context'],
                            $values['single'], $values['plural'], $trlelement['source'] );

                $method = 'trlcp'.$mode;
                $values['now'] = str_replace($values['single'], $newValues['single'], $values['before']);
                $values['now'] = str_replace($values['plural'], $newValues['plural'], $values['now']);
                $values['now'] = str_replace("\"".$values['context']."\",", "", $values['now']);
                $values['now'] = str_replace('\''.$values['context'].'\',', "", $values['now']);
                $values['now'] = str_replace($method, 'trlp', $values['now']);
            }
            $contents = str_replace($values['before'], $values['now'], $contents);
        }
        return $contents;
    }
}
