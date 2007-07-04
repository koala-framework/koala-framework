<?php
class Vps_Assets_Loader
{
    static public function load()
    {
        if (substr($_SERVER['SCRIPT_URL'], 0, 8)=='/assets/') {
            require_once 'Vps/Setup.php';
            $config = Vps_Setup::createConfig();
            $url = substr($_SERVER['SCRIPT_URL'], 8);

            if ($url == 'all.js') {
                require_once 'Vps/Assets/Dependencies.php';
                $dep = new Vps_Assets_Dependencies($config->asset, 'application/config.ini', 'dependencies');
                header('Content-Type: text/javascript');
                //echo $dep->getPackedAll('js');
                echo $dep->getContentsAll('js');
            } else if ($url == 'all.css') {
                require_once 'Vps/Assets/Dependencies.php';
                $dep = new Vps_Assets_Dependencies($config->asset, 'application/config.ini', 'dependencies');
                header('Content-Type: text/css');
                echo $dep->getContentsAll('css');
            } else {
                $type = substr($url, 0, strpos($url, '/'));
                $url = substr($url, strpos($url, '/')+1);
                require_once 'Vps/Assets/Dependencies.php';
                $paths = Vps_Assets_Dependencies::resolveAssetPaths($config->asset->toArray());
                if (isset($paths[$type])) {
                    if(!file_exists($paths[$type].$url)) {
                        die("file not found");
                    }
                    $headers = apache_request_headers();
                    $if_modified_since = $if_none_match = "";
                    if (isset($headers['If-Modified-Since'])) $if_modified_since = preg_replace('/;.*$/', '', $headers['If-Modified-Since']);
                    $lastModified = gmdate("D, d M Y H:i:s", filemtime($paths[$type].$url))." GMT";
                    if (false && $if_modified_since == $lastModified) {
                        header("HTTP/1.1 304 Not Modified");
                        header("Expires: ".gmdate("D, d M Y H:i:s",time()+24*60*60)." GMT");
                        header("Cache-Control: public, max-age=".(24*60*60));
                    } else {
                        header('Last-Modified: '.$lastModified);
                        header("Expires: ".gmdate("D, d M Y H:i:s",time()+24*60*60)." GMT");
                        header("Cache-Control: public, max-age=".(24*60*60));
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
                        } else {
                            die("invalid file type");
                        }
                        readfile($paths[$type].$url);
                    }
                } else {
                    die("unknown asset-type");
                }
            }
            exit;
        }
    }
}
