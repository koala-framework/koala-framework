<?php
class Kwc_Basic_LinkTag_Youtube_Video_ContentSender extends Kwf_Component_Abstract_ContentSender_Lightbox
{
    protected function _getOptions()
    {
        $ret = parent::_getOptions();
        $templateVars = $this->_data->getComponent()->getTemplateVars();
        $ret['width'] = $templateVars['config']['width'] + 20;
        $ret['height'] = $templateVars['config']['height'] + 18;
        return $ret;
    }
}
