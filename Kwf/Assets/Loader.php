<?php
class Kwf_Assets_Loader
{
    static public function load($url)
    {
        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        Kwf_Assets_Dispatcher::dispatch($url);
        try {
            $l = new self();
            $out = $l->getFileContents(substr($url, 8));
            Kwf_Media_Output::output($out);
        } catch (Kwf_Assets_NotFoundException $e) {
            throw new Kwf_Exception_NotFound();
        }
    }

    public function getFileContents($file, $language = null)
    {
        $ret = array();
        if (substr($file, -4)=='.gif') {
            $ret['mimeType'] = 'image/gif';
        } else if (substr($file, -4)=='.png') {
            $ret['mimeType'] = 'image/png';
        } else if (substr($file, -4)=='.jpg') {
            $ret['mimeType'] = 'image/jpeg';
        } else if (substr($file, -4)=='.mp4') {
            $ret['mimeType'] = 'video/mp4';
        } else if (substr($file, -5)=='.webm') {
            $ret['mimeType'] = 'video/webm';
        } else if (substr($file, -4)=='.css' || substr($file, -5)=='.scss') {
            $ret['mimeType'] = 'text/css; charset=utf-8';
            if (!Kwf_Assets_Dispatcher::allowSourceAccess()) throw new Kwf_Exception_AccessDenied();
        } else if (substr($file, -9)=='.printcss') {
            $ret['mimeType'] = 'text/css; charset=utf-8';
            if (!Kwf_Assets_Dispatcher::allowSourceAccess()) throw new Kwf_Exception_AccessDenied();
        } else if (substr($file, -3)=='.js') {
            $ret['mimeType'] = 'text/javascript; charset=utf-8';
            if (!Kwf_Assets_Dispatcher::allowSourceAccess()) throw new Kwf_Exception_AccessDenied();
        } else if (substr($file, -4)=='.swf') {
            $ret['mimeType'] = 'application/flash';
        } else if (substr($file, -4)=='.ico') {
            $ret['mimeType'] = 'image/x-icon';
        } else if (substr($file, -5)=='.html') {
            $ret['mimeType'] = 'text/html; charset=utf-8';
        } else if (substr($file, -4)=='.otf') { // für Schriften
            $ret['mimeType'] = 'application/octet-stream';
        } else if (substr($file, -4)=='.eot') { // für Schriften
            $ret['mimeType'] = 'application/vnd.ms-fontobject';
        } else if (substr($file, -4)=='.svg') { // für Schriften
            $ret['mimeType'] = 'image/svg+xml';
        } else if (substr($file, -4)=='.ttf') { // für Schriften
            $ret['mimeType'] = 'application/octet-stream';
        } else if (substr($file, -5)=='.woff') { // für Schriften
            $ret['mimeType'] = 'application/font-woff';
        } else if (substr($file, -6)=='.woff2') { // für Schriften
            $ret['mimeType'] = 'application/font-woff2';
        } else if (substr($file, -4)=='.htc') { // für ie css3
            $ret['mimeType'] = 'text/x-component';
        } else if (substr($file, -4)=='.pdf') {
            $ret['mimeType'] = 'application/pdf';
        } else if (substr($file, -4)=='.xml') {
            $ret['mimeType'] = 'application/xml; charset=utf-8';
        } else {
            throw new Kwf_Assets_NotFoundException("Invalid filetype ($file)");
        }

        if (substr($ret['mimeType'], 0, 5) == 'text/') {
            $ret['mtime'] = time();
            $file = new Kwf_Assets_Dependency_File($file);
            if (!$file->getAbsoluteFileName() || !file_exists($file->getAbsoluteFileName())) throw new Kwf_Exception_NotFound();
            $ret['contents'] = $file->getContents(null);
        } else {
            $fx = substr($file, 0, strpos($file, '/'));
            if (substr($fx, 0, 3) == 'fx_') {
                $cache = Kwf_Assets_Cache::getInstance();
                $cacheId = 'fileContents'.str_replace(array('/', '.', '-', ':'), array('_', '_', '_', '_'), $file);
                if (!$cacheData = $cache->load($cacheId)) {
                    if (substr($ret['mimeType'], 0, 6) != 'image/') {
                        throw new Kwf_Exception("Fx is only possible for images");
                    }
                    $im = new Imagick();
                    if (substr($file, -4)=='.ico') $im->setFormat('ico'); //required because imagick can't autodetect ico format
                    $file = new Kwf_Assets_Dependency_File(substr($file, strpos($file, '/')+1));
                    $im->readImage($file->getAbsoluteFileName());
                    $fx = explode('_', substr($fx, 3));
                    foreach ($fx as $i) {
                        $params = array();
                        if (($pos = strpos($i, '-')) !== false) {
                            $params = explode('-', substr($i, $pos + 1));
                            $i = substr($i, 0, $pos);
                        }
                        call_user_func(array('Kwf_Assets_Effects', $i), $im, $params);
                    }
                    $cacheData['mtime'] = $file->getMTime();
                    $cacheData['contents'] = $im->getImagesBlob();;
                    $im->destroy();
                    $cache->save($cacheData, $cacheId);
                }
                $ret['contents'] = $cacheData['contents'];
                $ret['mtime'] = time();
            } else {
                $ret['mtime'] = time();
                $file = new Kwf_Assets_Dependency_File($file);
                if (!file_exists($file->getAbsoluteFileName())) {
                    throw new Kwf_Exception_NotFound();
                }
                $ret['contents'] = $file->getContents(null);
            }
        }

        return $ret;
    }
}
