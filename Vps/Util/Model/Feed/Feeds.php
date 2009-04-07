<?php
class Vps_Util_Model_Feed_Feeds extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Util_Model_Feed_Row_Feed';
    protected $_dependentModels = array(
        'Entries' => 'Vps_Util_Model_Feed_Entries'
    );

    public function getOwnColumns()
    {
        return array('url', 'title', 'link', 'description', 'format', 'encoding');
    }

    public function getPrimaryKey()
    {
        return 'url';
    }
    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        $select = $this->select($where, $order, $limit, $start);
        $we = $select->getPart(Vps_Model_Select::WHERE_EQUALS);
        if ($we && isset($we['url'])) {
            $id = $we['url'];
        } else {
            $id = $select->getPart(Vps_Model_Select::WHERE_ID);
        }
        if ($id) {
            $dataKeys = array($id);
            return new $this->_rowsetClass(array(
                'dataKeys' => $dataKeys,
                'model' => $this
            ));
        } else {
            throw new Vps_Exception_NotYetImplemented();
        }
    }

    public function getRowByDataKey($key)
    {
        if (!isset($this->_rows[$key])) {
            $this->_rows[$key] = new $this->_rowClass(array(
                'url' => $key,
                'model' => $this
            ));
        }
        return $this->_rows[$key];
    }

    //kopiert von Zend_Feed!
    //und so umgebaut dass kein Http_Client verwendet wird
    public function findFeeds($uri)
    {
        $contents = file_get_contents($uri);

        // Parse the contents for appropriate <link ... /> tags
        @ini_set('track_errors', 1);
        $pattern = '~(<link[^>]+)/?>~i';
        $result = @preg_match_all($pattern, $contents, $matches);
        @ini_restore('track_errors');
        if ($result === false) {
            require_once 'Zend/Feed/Exception.php';
            throw new Zend_Feed_Exception("Internal error: $php_errormsg");
        }

        // Try to fetch a feed for each link tag that appears to refer to a feed
        $feeds = array();
        if (isset($matches[1]) && count($matches[1]) > 0) {
            foreach ($matches[1] as $link) {
                // force string to be an utf-8 one
                if (!mb_check_encoding($link, 'UTF-8')) {
                    $link = mb_convert_encoding($link, 'UTF-8');
                }
                $xml = @simplexml_load_string(rtrim($link, ' /') . ' />');
                if ($xml === false) {
                    continue;
                }
                $attributes = $xml->attributes();
                if (!isset($attributes['rel']) || !@preg_match('~^(?:alternate|service\.feed)~i', $attributes['rel'])) {
                    continue;
                }
                if (!isset($attributes['type']) ||
                        !@preg_match('~^application/(?:atom|rss|rdf)\+xml~', $attributes['type'])) {
                    continue;
                }
                if (!isset($attributes['href'])) {
                    continue;
                }
                try {
                    // checks if we need to canonize the given uri
                    try {
                        $uri = Zend_Uri::factory((string) $attributes['href']);
                    } catch (Zend_Uri_Exception $e) {
                        // canonize the uri
                        $pageUri = Zend_Uri::factory($uri);
                        $path = (string) $attributes['href'];
                        $query = $fragment = '';
                        if (substr($path, 0, 1) != '/') {
                            // add the current root path to this one
                            $path = rtrim($pageUri->getPath(), '/') . '/' . $path;
                        }
                        if (strpos($path, '?') !== false) {
                            list($path, $query) = explode('?', $path, 2);
                        }
                        if (strpos($query, '#') !== false) {
                            list($query, $fragment) = explode('#', $query, 2);
                        }
                        $uri = Zend_Uri::factory($pageUri->__toString());
                        $uri->setPath($path);
                        $uri->setQuery($query);
                        $uri->setFragment($fragment);
                    }
                    $feed = $this->getRow((string)$uri);
                } catch (Exception $e) {
                    continue;
                }
                $feeds[] = $feed;
            }
        }

        // Return the fetched feeds
        return $feeds;
    }
}
