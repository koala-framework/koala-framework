<?php
class Vpc_ParagraphsPassword_ExtConfig extends Vpc_Paragraphs_ExtConfig
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['paragraphs']['title'] = trlVps('Paragraphs');
        $ret['settings'] = $this->_getStandardConfig('vps.autoform', 'Settings', trlVps('Settings'));
        return $ret;
    }
}
