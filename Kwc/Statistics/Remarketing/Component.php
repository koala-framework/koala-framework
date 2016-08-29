<?php
class Kwc_Statistics_Remarketing_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['flags']['hasFooterIncludeCode'] = true;
        $ret['plugins'] = array('Kwc_Statistics_CookieBeforePlugin', 'Kwc_Statistics_CookieAfterPlugin');
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    protected function _getRemarketingId()
    {
        return $this->getData()->getBaseProperty('statistics.remarketing.id');
    }

    protected function _getRemarketingLabel()
    {
        return $this->getData()->getBaseProperty('statistics.remarketing.label');
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['id'] = (int)$this->_getRemarketingId();
        $ret['label'] = (string)$this->_getRemarketingLabel();
        $ret['ignoreCode'] = $this->getData()->getBaseProperty('statistics.ignore') || 
            $this->getData()->getBaseProperty('statistics.remarketing.ignore');
        return $ret;
    }
}
