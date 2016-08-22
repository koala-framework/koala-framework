<?php
class Kwf_Component_Cache_CacheTag_Test_Component extends Kwc_Abstract
{
    public static $test = 'foo';

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $r = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_CacheTag_Test_Model')->getRow(1);
        $ret['test'] = $r->test;
        return $ret;
    }

    public function getViewCacheSettings()
    {
        $ret = parent::getViewCacheSettings();
        $ret['cacheTag'] = 'asdf';
        return $ret;
    }
}
