<?php
class Kwc_Statistics_Piwik_Component extends Kwc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['domain'] = $this->_getDomain();
        $ret['id'] = $this->_getId();
        $ret['customVariables'] = $this->_getCustomVariables();
        return $ret;
    }

    public function hasContent()
    {
        return !!$this->_getId();
    }

    protected function _getDomain()
    {
        $cfg = Kwf_Config::getValueArray('statistic');
        return isset($cfg['piwikDomain']) ? $cfg['piwikDomain'] : null;
    }

    protected function _getId()
    {
        $cfg = Kwf_Config::getValueArray('statistic');
        return isset($cfg['piwikId']) ? $cfg['piwikId'] : null;
    }

    protected function _getCustomVariables()
    {
        return array();
    }
}
