<?php
class Vps_Media_Output
{
    //hilfsfunktion zur ausgabe von dateien
    //todo: bei assets verwenden und dahin verschieben
    //todo: gzip komprimierung usw einbaun
    public static function output($target, $mimeType, $downloadFilename = false)
    {
        if (!is_file($target)) {
            throw new Vps_Controller_Action_Web_Exception("File '$target' not found.");
        }

        $headers = apache_request_headers();
        $lastModifiedString = gmdate("D, d M Y H:i:s \G\M\T", filemtime($target));
        $etag = md5($target . $lastModifiedString);
        if ((isset($headers['If-Modified-Since']) &&
                $headers['If-Modified-Since'] == $lastModifiedString) ||
            (isset($headers['If-None-Match']) &&
                $headers['If-Modified-Since'] == $etag)
        ) {
            header('HTTP/1.1 304 Not Modified');
            header('ETag: ' . $etag);
            header('Last-Modified: ' . $lastModifiedString);
        } else {
            header('Content-type: ' . $mimeType);
            header('Last-Modified: ' . $lastModifiedString);
            header('Content-Length: ' . filesize($target));
            header('ETag: ' . $etag);
            if ($downloadFilename) {
                header('Content-Disposition', 'attachment; filename="' . $downloadFilename . '"');
            }
            readfile($target);
        }
        die();
    }
}
