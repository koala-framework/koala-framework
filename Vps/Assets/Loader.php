<?php
class Vps_Assets_Loader
{
    static public function getAssetPath($url, $paths)
    {
        $type = substr($url, 0, strpos($url, '/'));
        $url = substr($url, strpos($url, '/')+1);
        if (isset($paths[$type])) {
            if(!file_exists($paths[$type].$url)) {
                return null;
            }
        } else {
            return null;
        }
        return $paths[$type].$url;
    }

    static public function load()
    {
        require_once 'Vps/Loader.php';
        Vps_Loader::registerAutoload();
        if (substr($_SERVER['SCRIPT_URL'], 0, 8)=='/assets/') {

            $headers = apache_request_headers();
            $http_if_modified_since = "";
            if (isset($headers['If-Modified-Since'])) $http_if_modified_since = preg_replace('/;.*$/', '', $headers['If-Modified-Since']);

            $encoding = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
                        ? 'gzip' : (strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate')
                        ? 'deflate' : 'none');

            $config = Vps_Setup::createConfig();
            $url = substr($_SERVER['SCRIPT_URL'], 8);
            if ($url == 'all.js' || $url == 'all.css') {
                if ($url == 'all.js') {
                    header('Content-Type: text/javascript');
                    $fileType = 'js';
                } else {
                    header('Content-Type: text/css');
                    $fileType = 'css';
                }

                $frontendOptions = array(
                    'lifetime' => null
                );
                $backendOptions = array(
                    'cache_dir' => 'application/cache/assets/'
                );
                $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

                if ((!$lastModified = $cache->load($fileType.$encoding.'AllLastModified'))
                    || !$cache->test($fileType.$encoding.'AllPacked')) {

                    $dep = new Vps_Assets_Dependencies($config);
                    $contents = $dep->getPackedAll($fileType);
                    $contents = self::_encode($contents, $encoding);
                    $cache->save($contents, $fileType.$encoding.'AllPacked');

                    $lm = gmdate("D, d M Y H:i:s \G\M\T", time());
                    header('Last-Modified: '.$lm);
                    $cache->save($lm, $fileType.$encoding.'AllLastModified');
                } else {
                    header('Last-Modified: '.$lastModified);
                }
                if ($http_if_modified_since == $lastModified) {
                    header('HTTP/1.1 304 Not Modified');
                    exit;
                }
                header('Cache-Control: must-revalidate, max-age='.(24*60*60));
                if (!isset($contents)) {
                    $contents = $cache->load($fileType.$encoding.'AllPacked');
                }
                header ("Content-Encoding: " . $encoding);
                echo $contents;
            } else {
                $paths = $config->path->toArray();
                $assetPath = self::getAssetPath($url, $paths);
                if (!$assetPath) {
                    header("HTTP/1.0 404 Not Found");
                    die("file not found");
                }
                $lastModified = gmdate("D, d M Y H:i:s \G\M\T", filemtime($assetPath));
                if ($http_if_modified_since == $lastModified) {
                    header("HTTP/1.1 304 Not Modified");
                } else {
                    header('Last-Modified: '.$lastModified);
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
                    $contents = file_get_contents($assetPath);
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
