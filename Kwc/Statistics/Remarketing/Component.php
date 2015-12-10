<?php
class Kwc_Statistics_Remarketing_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
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
        $ret = null;
        if (!$this->getData()->getBaseProperty('statistics.ignore') &&
            !$this->getData()->getBaseProperty('statistics.remarketing.ignore')
        ) {
            $ret = $this->getData()->getBaseProperty('statistics.remarketing.id');
        }
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['id'] = $this->_getRemarketingId();
        if ($ret['id'] && !is_string($ret['id'])) {
            throw new Kwf_Exception("RemarketingId must be a string, '".gettype($ret['id'])."' given");
        }
        return $ret;
    }
}
