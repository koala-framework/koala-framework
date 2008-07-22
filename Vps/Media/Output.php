<?php
class Vps_Media_Output
{
    public static function getEncoding()
    {
        $headers = apache_request_headers();
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            $encoding = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
                        ? 'gzip' : (strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate')
                        ? 'deflate' : 'none');
        } else {
            $encoding = 'none';
        }
        return $encoding;
    }

    public static function output($file, $mimeType = null, $downloadFilename = false)
    {
        if (is_string($file)) {
            if (!is_file($file)) {
                throw new Vps_Controller_Action_Web_Exception("File '$target' not found.");
            }
            $file = array(
                'contents' => file_get_contents($file),
                'mtime' => filemtime($file),
            );
        }
        if ($mimeType) $file['mimeType'] = $mimeType;
        if ($downloadFilename) $file['downloadFilename'] = $downloadFilename;

        $headers = apache_request_headers();
        if (isset($file['mtime'])) {
            $lastModifiedString = gmdate("D, d M Y H:i:s \G\M\T", $file['mtime']);
        }
        header('Cache-Control: public, max-age='.(24*60*60));
        header('Expires: '.gmdate("D, d M Y H:i:s \G\M\T", time()+(24*60*60)));
        header('Pragma: public');
        if (isset($file['mtime']) && isset($headers['If-Modified-Since']) &&
                $headers['If-Modified-Since'] == $lastModifiedString) {
            header('Not Modified', true, 304);
            header('Last-Modified: '.$headers['If-Modified-Since']);
            exit;
        } else if (isset($file['etag']) && isset($headers['If-None-Match']) &&
                $headers['If-None-Match'] == $file['etag']) {
            header('Not Modified', true, 304);
            header('ETag: '.$headers['If-None-Match']);
            exit;
        } else {
            if (isset($file['etag'])) header('ETag: ' . $file['etag'], true);
            if (isset($file['mtime'] )) header('Last-Modified: ' . $lastModifiedString, true);
            header('Accept-Ranges: none');
            if (isset($file['downloadFilename'])) {
                header('Content-Disposition', 'attachment; filename="' . $file['downloadFilename'] . '"');
            }
            if (isset($file['encoding'])) {
                header("Content-Encoding: " . $file['encoding']);
            } else {
                if (substr($file['mimeType'], 0, 5) == 'text/') {
                    $encoding = self::getEncoding();
                    $file['contents'] = self::encode($file['contents'], $encoding);
                } else {
                    $encoding = 'none';
                }
                header("Content-Encoding: " . $encoding);
            }
            header('Content-Type: ' . $file['mimeType']);
            header('Content-Length: ' . strlen($file['contents']));
            echo $file['contents'];
        }
        exit;
    }

    static public function encode($contents, $encoding)
    {
        if ($encoding != 'none') {
            return gzencode($contents, null, ($encoding=='gzip') ? FORCE_GZIP : FORCE_DEFLATE);
        } else {
            return $contents;
        }
    }

}
