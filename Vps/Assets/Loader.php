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
            foreach($cssConfig as $k=>$i) {
	        $contents = preg_replace('#\\$'.preg_quote($k).'([^a-z0-9A-Z])#', "$i\\1", $contents);
            }
        }
        $version = Zend_Registry::get('config')->application->version;
        $contents = str_replace('{$application.version}', $version, $contents);
        return $contents;
    }

    static public function load()
    {
        require_once 'Vps/Loader.php';
        Vps_Loader::registerAutoload();
        if (substr($_SERVER['REQUEST_URI'], 0, 8)=='/assets/') {

            $headers = apache_request_headers();
            $http_if_modified_since = "";
            if (isset($headers['If-Modified-Since'])) $http_if_modified_since = preg_replace('/;.*$/', '', $headers['If-Modified-Since']);

            if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
                $encoding = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
                            ? 'gzip' : (strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate')
                            ? 'deflate' : 'none');
            } else {
                $encoding = 'none';
            }

            $config = Vps_Setup::createConfig();
            $url = substr($_SERVER['REQUEST_URI'], 8);
            if (preg_match('#^All([a-z]+)\\.(js|css)$#i', $url, $m)) {
                if ($m[2] == 'js') {
                    header('Content-Type: text/javascript');
                    $fileType = 'js';
                } else {
                    header('Content-Type: text/css');
                    $fileType = 'css';
                }
                $section = $m[1];

                $frontendOptions = array(
                    'lifetime' => null
                );
                $backendOptions = array(
                    'cache_dir' => 'application/cache/assets/'
                );
                $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

                if ((!$lastModified = $cache->load($fileType.$encoding.$section.'LastModified'))
                    || !$cache->test($fileType.$encoding.$section.'Packed')) {

                    $dep = new Vps_Assets_Dependencies($config->assets->$section, $config);
                    $contents = $dep->getPackedAll($fileType);
                    $contents = self::_encode($contents, $encoding);
                    $cache->save($contents, $fileType.$encoding.$section.'Packed');

                    $lm = gmdate("D, d M Y H:i:s \G\M\T", time());
                    header('Last-Modified: '.$lm);
                    $cache->save($lm, $fileType.$encoding.$section.'LastModified');
                } else {
                    header('Last-Modified: '.$lastModified);
                }
                if ($http_if_modified_since == $lastModified) {
                    header('HTTP/1.1 304 Not Modified');
                    exit;
                }
                header('Cache-Control: must-revalidate, max-age='.(24*60*60));
                if (!isset($contents)) {
                    $contents = $cache->load($fileType.$encoding.$section.'Packed');
                }
                header ("Content-Encoding: " . $encoding);
                echo $contents;
            } else {
                $assetPath = self::getAssetPath($url, $config->path);
                if (!$assetPath) {
                    header("HTTP/1.0 404 Not Found");
                    die("file not found");
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
                    } else {
                        header("HTTP/1.0 404 Not Found");
                        die("invalid file type");
                    }
                    $contents = self::getFileContents($url, $config->path);
                    header ("Content-Encoding: " . $encoding);
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
}
