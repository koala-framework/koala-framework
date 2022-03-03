<?php
class Kwf_Media_Output
{
    const ENCODING_NONE = 'identity';
    const ENCODING_GZIP = 'gzip';
    const ENCODING_DEFLATE = 'deflate';

    public static function getEncoding()
    {
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            $encoding = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
                        ? self::ENCODING_GZIP : (strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate')
                        ? self::ENCODING_DEFLATE : self::ENCODING_NONE);
        } else {
            $encoding = self::ENCODING_NONE;
        }
        return $encoding;
    }

    public static function outputWithoutShutdown($file)
    {
        $headers = array();
        if (isset($_SERVER['HTTPS'])) $headers['Https'] = $_SERVER['HTTPS'];
        if (isset($_SERVER['HTTP_USER_AGENT'])) $headers['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) $headers['If-Modified-Since'] = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) $headers['If-None-Match'] = $_SERVER['HTTP_IF_NONE_MATCH'];
        if (isset($_SERVER['HTTP_RANGE'])) $headers['Range'] = $_SERVER['HTTP_RANGE'];
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
            //self::_readfileChunked($data['file']);
        }
        $ret = array(
            'responseCode' => $data['responseCode'],
            'contentLength' => $data['contentLength'],
        );
        if (isset($data['encoding'])) $ret['encoding'] = $data['encoding'];
        return $ret;
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
            } else if (isset($file['contentsCallback'])) {
                //contents will be fetched on demand thru contentsCallback
            } else {
                throw new Kwf_Exception_NotFound();
            }
        }
        if (isset($file['mtime'])) {
            $lastModifiedString = gmdate("D, d M Y H:i:s \G\M\T", $file['mtime']);
        }
        $lifetime = (24*60*60*7*4);
        if (isset($file['lifetime'])) {
            if ($file['lifetime'] === false) {
                $lifetime = false;
            } else {
                $lifetime = $file['lifetime'];
            }
        }
        // According to following link it's not possible in IE<9 to download
        // any file with Pragma set to "no-cache" or order of Cache-Control other
        // than "no-store, no-cache" when using a SSL connection.
        // http://blogs.msdn.com/b/ieinternals/archive/2009/10/02/internet-explorer-cannot-download-over-https-when-no-cache.aspx

        // The order of Cache-Control is correct in default-implementation so
        // it's only required to reset Pragma to nothing.

        // The definition of Pragma can be found here (http://www.ietf.org/rfc/rfc2616.txt)
        // at chapter 14.32
        if ($lifetime) {
            if (isset($headers['Https']) && isset($headers['User-Agent'])
                && preg_match('/msie [6-8]/i', $headers['User-Agent'])
            ) {
                $ret['headers'][] = 'Cache-Control: private, max-age='.$lifetime;
                $ret['headers'][] = 'Pragma:';
            } else {
                $ret['headers'][] = 'Cache-Control: public, max-age='.$lifetime;
                $ret['headers'][] = 'Expires: '.gmdate("D, d M Y H:i:s \G\M\T", time()+$lifetime);
                $ret['headers'][] = 'Pragma: public';
            }
        } else {
            $ret['headers'][] = 'Cache-Control: private';
            $ret['headers'][] = 'Pragma:';
        }
        if (isset($file['mtime']) && isset($headers['If-Modified-Since']) &&
                $file['mtime'] <= strtotime($headers['If-Modified-Since'])) {
            $ret['responseCode'] = 304;
            $ret['contentLength'] = 0;
            if (PHP_SAPI == 'cgi-fcgi') {
                $ret['headers'][] = "Status: 304 Not Modified";
            } else {
                $ret['headers'][] = array('Not Modified', true, 304);
            }
            $ret['headers'][] = 'Last-Modified: '.$headers['If-Modified-Since'];
        } else if (isset($file['etag']) && isset($headers['If-None-Match']) &&
                $headers['If-None-Match'] == $file['etag']) {
            $ret['responseCode'] = 304;
            $ret['contentLength'] = 0;
            if (PHP_SAPI == 'cgi-fcgi') {
                $ret['headers'][] = "Status: 304 Not Modified";
            } else {
                $ret['headers'][] = array('Not Modified', true, 304);
            }
            $ret['headers'][] = 'ETag: '.$headers['If-None-Match'];
        } else {
            if (substr($file['mimeType'], 0, 12) != 'application/') {
                $ret['headers'][] = 'Accept-Ranges: bytes';
                if (isset($headers['Range'])) {
                    $ret['responseCode'] = 206;
                    $ret['headers'][] = "HTTP/1.1 206 Partial Content";
                } else {
                    $ret['responseCode'] = 200;
                }
            } else {
                $ret['headers'][] = 'Accept-Ranges: none';
                $ret['responseCode'] = 200;
            }

            if (isset($file['etag'])) {
                $ret['headers'][] = 'ETag: ' . $file['etag'];
            } else {
                //wird benötigt für IE der sonst den download verweigert
                $ret['headers'][] = 'ETag: tag';
            }
            if (isset($file['mtime'] )) $ret['headers'][] = 'Last-Modified: ' . $lastModifiedString;
            if (isset($file['downloadFilename']) && $file['downloadFilename']) {
                $ret['headers'][] = 'Content-Disposition: attachment; filename="' . $file['downloadFilename'] . '"';
            }
            if (isset($file['filename']) && $file['filename']) {
                $ret['headers'][] = 'Content-Disposition: inline; filename="' . $file['filename'] . '"';
            }
            if (isset($file['noindex']) && $file['noindex']) $ret['headers'][] = 'X-Robots-Tag: noindex';
            $encoding = self::ENCODING_NONE;
            if (isset($file['encoding'])) {
                $encoding = $file['encoding'];
            } else {
                if (substr($file['mimeType'], 0, 5) == 'text/' || $file['mimeType'] == 'application/json') {
                    if (isset($file['contents'])) {
                        $encoding = self::getEncoding($headers);
                        $file['contents'] = self::encode($file['contents'], $encoding);
                    } else if (isset($file['contentsCallback'])) {
                        $encoding = self::getEncoding($headers);
                        if (isset($file['cache'])) {
                            $file['contents'] = $file['cache']->load($file['cacheId'].'_'.$encoding);
                            if ($file['contents']===false) {
                                $contents = call_user_func($file['contentsCallback'], $file);
                                $file['contents'] = self::encode($contents, $encoding);
                                $file['cache']->save($file['contents'], $file['cacheId'].'_'.$encoding);
                            }
                        } else {
                            $contents = call_user_func($file['contentsCallback'], $file);
                            $file['contents'] = self::encode($contents, $encoding);
                        }
                    } else {
                        //don't encode file (as they are usually large and read using readfile)
                    }
                }
            }
            $ret['encoding'] = $encoding;
            if ($encoding != self::ENCODING_NONE) {
                $ret['headers'][] = 'Content-Encoding: ' . $encoding;
            }
            $ret['headers'][] = 'Content-Type: ' . $file['mimeType'];
            if (!isset($file['contents']) && isset($file['contentsCallback'])) {
                if (isset($file['cache'])) {
                    $file['contents'] = $file['cache']->load($file['cacheId'].'_'.$encoding);
                    if ($file['contents']===false) {
                        $file['contents'] = call_user_func($file['contentsCallback'], $file);
                        $file['cache']->save($file['contents'], $file['cacheId'].'_'.$encoding);
                    }
                } else {
                    $file['contents'] = call_user_func($file['contentsCallback'], $file);
                }
            }
            if (isset($file['contents'])) {
                if (isset($headers['Range'])) {
                    if (!preg_match('#bytes#', $headers['Range'])) {
                        throw new Kwf_Exception('wrong Range-Type');
                    }
                    $range = explode('=', $headers['Range']);
                    $range = explode('-', $range[1]);
                    $ret['contents'] = substr($file['contents'], $range[0], $range[1]+1);
                    $ret['contentLength'] = strlen($ret['contents']);
                    $ret['headers'][] = 'Content-Length: ' . $ret['contentLength'];
                    $ret['headers'][] = 'Content-Range: bytes ' . $range[0] . '-'
                        . $range[1] . '/' . strlen($file['contents']);
                } else {
                    $ret['contentLength'] = strlen($file['contents']);
                    $ret['headers'][] = 'Content-Length: ' . $ret['contentLength'];
                    $ret['contents'] = $file['contents'];
                }
            } else if (isset($file['file'])) {
                if (isset($headers['Range'])) {
                    $range = explode('=', $headers['Range']);
                    $range = explode('-', $range[1]);
                    if (!$range[1]) {
                        $range[1] = filesize($file['file'])-1;
                    }
                    $ret['contents'] = self::_getPartialFileContent($file['file'], $range);
                    $ret['contentLength'] = strlen($ret['contents']);
                    $ret['headers'][] = 'Content-Length: ' . $ret['contentLength'];
                    $ret['headers'][] = 'Content-Range: bytes ' . $range[0] . '-'
                        . $range[1] . '/' . filesize($file['file']);
                } else {
                    $ret['contentLength'] = filesize($file['file']);
                    $ret['headers'][] = 'Content-Length: ' . $ret['contentLength'];
                    $ret['file'] = $file['file'];
                }
            } else {
                throw new Kwf_Exception("contents not set");
            }
        }
        return $ret;
    }

    static public function encode($contents, $encoding)
    {
        if ($encoding != self::ENCODING_NONE) {
            return gzencode($contents, 9, ($encoding==self::ENCODING_GZIP) ? FORCE_GZIP : FORCE_DEFLATE);
        } else {
            return $contents;
        }
    }

    // returns the partial content from a file
    private static function _getPartialFileContent($file, $range)
    {
        $length = $range[1]-$range[0]+1;

        if( !$handle = fopen($file, 'r') )
            throw new Kwf_Exception(sprintf("Could not get handle for file %s", $file));
        if( fseek($handle, $range[0], SEEK_SET) == -1 )
            throw new Kwf_Exception(sprintf("Could not seek to byte offset {$rage[0]}"));

        $ret = fread($handle, $length);
        return $ret;
    }

    /**
     * @see http://php.net/manual/de/function.readfile.php#54295
     */
    private static function _readfileChunked($filename, $retbytes=true) {
        $chunksize = 1*(1024*1024); // how many bytes per chunk
        $buffer = '';
        $cnt =0;
        $handle = fopen($filename, 'rb');
        if ($handle === false) { return false; }
        while (!feof($handle)) {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            ob_flush();
            flush();
            if ($retbytes) {
                $cnt += strlen($buffer);
            }
        }
        $status = fclose($handle);
        if ($retbytes && $status) {
            return $cnt; // return num. bytes delivered like readfile() does.
        }
        return $status;
    }
}
