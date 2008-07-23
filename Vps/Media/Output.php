<?php
class Vps_Media_Output
{
    public static function getEncoding()
    {
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            $encoding = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
                        ? 'gzip' : (strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate')
                        ? 'deflate' : 'none');
        } else {
            $encoding = 'none';
        }
        return $encoding;
    }

    public static function output($file)
    {
        $data = self::getOutputData($file, apache_request_headers());
        foreach ($data['headers'] as $h) {
            if (is_array($h)) {
                call_user_func_array('header', $h);
            } else {
                header($h);
            }
        }
        echo $data['contents'];
        exit;
    }

    /**
     * PUBLIC METHOD FOR UNIT TESTING ONLY !
     */
    public static function getOutputData($file, array $headers)
    {
        $ret = array('headers' => array(), 'contents' => '');

        if (!isset($file['contents'])) {
            if (!isset($file['file'])) {
                if (!is_file($file['file'])) {
                    throw new Vps_Controller_Action_Web_Exception("File '$file[file]' not found.");
                }
                $file['contents'] = file_get_contents($file);
                $file['mtime'] = filemtime($file);
            } else {
                throw new Vps_Exception("contents for file has to be set");
            }
        }
        if (isset($file['mtime'])) {
            $lastModifiedString = gmdate("D, d M Y H:i:s \G\M\T", $file['mtime']);
        }
        $ret['headers'][] = 'Cache-Control: public, max-age='.(24*60*60);
        $ret['headers'][] = 'Expires: '.gmdate("D, d M Y H:i:s \G\M\T", time()+(24*60*60));
        $ret['headers'][] = 'Pragma: public';
        if (isset($file['mtime']) && isset($headers['If-Modified-Since']) &&
                $headers['If-Modified-Since'] == $lastModifiedString) {
            $ret['headers'][] = array('Not Modified', true, 304);
            $ret['headers'][] = 'Last-Modified: '.$headers['If-Modified-Since'];
        } else if (isset($file['etag']) && isset($headers['If-None-Match']) &&
                $headers['If-None-Match'] == $file['etag']) {
            $ret['headers'][] = array('Not Modified', true, 304);
            $ret['headers'][] = 'ETag: '.$headers['If-None-Match'];
        } else {
            if (isset($file['etag'])) $ret['headers'][] = 'ETag: ' . $file['etag'];
            if (isset($file['mtime'] )) $ret['headers'][] = 'Last-Modified: ' . $lastModifiedString;
            $ret['headers'][] = 'Accept-Ranges: none';
            if (isset($file['downloadFilename']) && $file['downloadFilename']) {
                $ret['headers'][] = 'Content-Disposition: attachment; filename="' . $file['downloadFilename'] . '"';
            }
            if (isset($file['encoding'])) {
                $ret['headers'][] = "Content-Encoding: " . $file['encoding'];
            } else {
                if (substr($file['mimeType'], 0, 5) == 'text/') {
                    $encoding = self::getEncoding($headers);
                    $file['contents'] = self::encode($file['contents'], $encoding);
                } else {
                    $encoding = 'none';
                }
                $ret['headers'][] = "Content-Encoding: " . $encoding;
            }
            $ret['headers'][] = 'Content-Type: ' . $file['mimeType'];
            $ret['headers'][] = 'Content-Length: ' . strlen($file['contents']);
            $ret['contents'] = $file['contents'];
        }
        return $ret;
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
