<?php
class Kwc_ParagraphsPassword_ExtConfig extends Kwc_Paragraphs_ExtConfig
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['paragraphs']['title'] = trlKwf('Paragraphs');
        $ret['settings'] = $this->_getStandardConfig('kwf.autoform', 'Settings', trlKwf('Settings'));
        return $ret;
    }
}
