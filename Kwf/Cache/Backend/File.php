<?php
require_once 'Zend/Cache/Backend/File.php';
class Kwf_Cache_Backend_File extends Zend_Cache_Backend_File
{
    public function __construct(array $options = array())
    {
        if (!isset($options['cache_file_umask'])) {
            $options['cache_file_umask'] = 0666;
        }
        if (!isset($options['hashed_directory_umask'])) {
            $options['hashed_directory_umask'] = 0777;
        }
        $this->_options['compression'] = isset($options['compression']) ? (bool)$options['compression'] : false;
        parent::__construct($options);
    }

    
    public function load($id, $doNotTestCacheValidity = false)
    {
        $ret = parent::load($id, $doNotTestCacheValidity);
        if ($this->_options['compression']) $ret = gzinflate($ret);
        return $ret;
    }

    public function loadWithMetadata($id, $doNotTestCacheValidity = false)
    {
        $contents = $this->load($id, $doNotTestCacheValidity);
        if ($contents === false) return false;
        $md = $this->getMetadatas($id);
        return array(
            'contents' => $contents,
            'expire' => $md['expire']
        );
    }

    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        if ($this->_options['compression']) $data = gzdeflate($data);
        return parent::save($data, $id, $tags, $specificLifetime);
    }
}
