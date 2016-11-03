<?php
class Kwf_Component_Partial_Abstract
{
    protected $_params;

    public function __construct($params)
    {
        $this->_params = $params;
    }

    public function getIds()
    {
        return array();
    }

    public function getParam($param, $necessary = true)
    {
        if ($necessary && !isset($this->_params[$param]))
            throw new Kwf_Exception('Param needed for Partial: ' . $param);
        if (!isset($this->_params[$param])) return null;
        return $this->_params[$param];
    }

    public static function useViewCache($componentId, $params)
    {
        if (isset($params['disableCache']) && $params['disableCache']) {
            return false;
        }
        if (isset($params['disableCacheParams'])) {
            return array(
                'callback' => array(
                    'Kwf_Component_Partial_Abstract',
                    '_useViewCacheDynamic'
                ),
                'args' => array(
                    $params['disableCacheParams']
                )
            );
        }
        return true;
    }

    public static function _useViewCacheDynamic($disableCacheParams)
    {
        foreach ($disableCacheParams as $param) {
            if (isset($_REQUEST[$param])) return false;
        }
        return true;
    }
}
