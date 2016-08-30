<?php
class Kwc_Basic_Flash_Upload_Component extends Kwc_Abstract_Flash_Upload_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Flash.Upload');
        $ret['ownModel'] = 'Kwc_Basic_Flash_Upload_Model';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'kwf_upload_id_media';
        return $ret;
    }

    protected function _getFlashData()
    {
        $ret = parent::_getFlashData();
        $ret['url'] = $this->_getUploadUrl();
        $ret['width'] = $this->_getRow()->width;
        $ret['height'] = $this->_getRow()->height;
        $ret['params'] = array(
            'allowfullscreen' => $this->_getRow()->allow_fullscreen ? 'true' : 'false',
            'menu' => $this->_getRow()->menu ? 'true' : 'false'
        );
        return $ret;
    }

    protected function _getFlashVars()
    {
        $ret = parent::_getFlashVars();
        foreach ($this->_getRow()->getChildRows('FlashVars') as $var) {
            if (!empty($var->key)) {
                $ret[$var->key] = $var->value;
            }
        }
        return $ret;
    }
}