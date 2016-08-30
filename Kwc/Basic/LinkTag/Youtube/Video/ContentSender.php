<?php
class Kwc_Basic_LinkTag_Youtube_Video_ContentSender extends Kwf_Component_Abstract_ContentSender_Lightbox
{
    protected function _getOptions()
    {
        $ret = parent::_getOptions();
        $config = $this->_data->getComponent()->getConfig();
        $ret['width'] = $config['width'] + 20;
        $ret['height'] = $config['height'] + 18;
        $ret['adaptHeight'] = true;
        return $ret;
    }
}
