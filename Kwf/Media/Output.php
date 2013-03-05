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
        } else {
            // According to following link it's not possible in IE<9 to download
            // any file with Pragma set to "no-cache" or order of Cache-Control other
            // than "no-store, no-cache" when using a SSL connection.
            // http://blogs.msdn.com/b/ieinternals/archive/2009/10/02/internet-explorer-cannot-download-over-https-when-no-cache.aspx

            // The order of Cache-Control is correct in default-implementation so
            // it's only required to reset Pragma to nothing.

            // The definition of Pragma can be found here (http://www.ietf.org/rfc/rfc2616.txt)
            // at chapter 14.32
            $ret['headers'][] = 'Pragma:';
        }
        if (isset($file['mtime']) && isset($headers['If-Modified-Since']) &&
                $headers['If-Modified-Since'] == $lastModifiedString) {
            $ret['responseCode'] = 304;
            $ret['contentLength'] = 0;
            $ret['headers'][] = array('Not Modified', true, 304);
            $ret['headers'][] = 'Last-Modified: '.$headers['If-Modified-Since'];
        } else if (isset($file['etag']) && isset($headers['If-None-Match']) &&
                $headers['If-None-Match'] == $file['etag']) {
            $ret['responseCode'] = 304;
            $ret['contentLength'] = 0;
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
            $encoding = 'none';
            if (isset($file['encoding'])) {
                $encoding = $file['encoding'];
            } else {
                if (substr($file['mimeType'], 0, 5) == 'text/') {
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
            $ret['headers'][] = 'Content-Encoding: ' . $encoding;
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
                $ret['contentLength'] = strlen($file['contents']);
                $ret['headers'][] = 'Content-Length: ' . $ret['contentLength'];
                $ret['contents'] = $file['contents'];
            } else if (isset($file['file'])) {
                $ret['contentLength'] = filesize($file['file']);
                $ret['headers'][] = 'Content-Length: ' . $ret['contentLength'];
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
