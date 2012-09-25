<?php
class Kwf_Media_Output
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

    public static function outputWithoutShutdown($file)
    {
        $headers = array();
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) $headers['If-Modified-Since'] = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) $headers['If-None-Match'] = $_SERVER['HTTP_IF_NONE_MATCH'];
        $data = self::getOutputData($file, $headers);
        foreach ($data['headers'] as $h) {
            if (is_array($h)) {
                call_user_func_array('header', $h);
            } else {
                header($h);
            }
        }
        if (isset($data['contents'])) {
            echo $data['contents'];
        } else if (isset($data['file'])) {
            readfile($data['file']);
        }
        return array(
            'responseCode' => $data['responseCode'],
        );
    }

    public static function output($file)
    {
        self::outputWithoutShutdown($file);
        Kwf_Benchmark::shutDown();
        exit;
    }

    /**
     * PUBLIC METHOD FOR UNIT TESTING ONLY !
     */
    public static function getOutputData($file, array $headers)
    {
        $ret = array('headers' => array());

        if (!isset($file['contents'])) {
            if (isset($file['file'])) {
                if (!is_file($file['file'])) {
                    throw new Kwf_Exception_NotFound("File '$file[file]' not found.");
                }
                if (!isset($file['mtime'])) $file['mtime'] = filemtime($file['file']);
            } else {
                throw new Kwf_Exception_NotFound();
            }
        }
        if (isset($file['mtime'])) {
            $lastModifiedString = gmdate("D, d M Y H:i:s \G\M\T", $file['mtime']);
        }
        $lifetime = (24*60*60);
        if (isset($file['lifetime'])) {
            if ($file['lifetime'] === false) {
                $lifetime = false;
            } else {
                $lifetime = $file['lifetime'];
            }
        }
        if ($lifetime) {
            $ret['headers'][] = 'Cache-Control: public, max-age='.$lifetime;
            $ret['headers'][] = 'Expires: '.gmdate("D, d M Y H:i:s \G\M\T", time()+$lifetime);
            $ret['headers'][] = 'Pragma: public';
        }
        if (isset($file['mtime']) && isset($headers['If-Modified-Since']) &&
                $headers['If-Modified-Since'] == $lastModifiedString) {
            $ret['responseCode'] = 304;
            $ret['headers'][] = array('Not Modified', true, 304);
            $ret['headers'][] = 'Last-Modified: '.$headers['If-Modified-Since'];
        } else if (isset($file['etag']) && isset($headers['If-None-Match']) &&
                $headers['If-None-Match'] == $file['etag']) {
            $ret['responseCode'] = 304;
            $ret['headers'][] = array('Not Modified', true, 304);
            $ret['headers'][] = 'ETag: '.$headers['If-None-Match'];
        } else {
            $ret['responseCode'] = 200;
            if (isset($file['etag'])) {
                $ret['headers'][] = 'ETag: ' . $file['etag'];
            } else {
                //wird benötigt für IE der sonst den download verweigert
                $ret['headers'][] = 'ETag: tag';
            }
            if (isset($file['mtime'] )) $ret['headers'][] = 'Last-Modified: ' . $lastModifiedString;
            $ret['headers'][] = 'Accept-Ranges: none';
            if (isset($file['downloadFilename']) && $file['downloadFilename'] &&
                substr($file['mimeType'], 0, 6) != 'image/'
            ) {
                $ret['headers'][] = 'Content-Disposition: attachment; filename="' . $file['downloadFilename'] . '"';
            }
            if (isset($file['filename']) && $file['filename']) {
                $ret['headers'][] = 'Content-Disposition: inline; filename="' . $file['filename'] . '"';
            }
            if (isset($file['encoding'])) {
                $ret['headers'][] = "Content-Encoding: " . $file['encoding'];
            } else {
                if (substr($file['mimeType'], 0, 5) == 'text/' && isset($file['contents'])) {
                    $encoding = self::getEncoding($headers);
                    $file['contents'] = self::encode($file['contents'], $encoding);
                } else {
                    $encoding = 'none';
                }
                $ret['headers'][] = "Content-Encoding: " . $encoding;
            }
            $ret['headers'][] = 'Content-Type: ' . $file['mimeType'];
            if (isset($file['contents'])) {
                $ret['headers'][] = 'Content-Length: ' . strlen($file['contents']);
                $ret['contents'] = $file['contents'];
            } else if (isset($file['file'])) {
                $ret['headers'][] = 'Content-Length: ' . filesize($file['file']);
                $ret['file'] = $file['file'];
            } else {
                throw new Kwf_Exception("contents not set");
            }
        }
        return $ret;
    }

    static public function encode($contents, $encoding)
    {
        if ($encoding != 'none') {
            return gzencode($contents, 9, ($encoding=='gzip') ? FORCE_GZIP : FORCE_DEFLATE);
        } else {
            return $contents;
        }
    }

}
