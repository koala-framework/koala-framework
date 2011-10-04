<?php
class Vpc_Basic_Flash_Upload_Component extends Vpc_Abstract_Flash_Upload_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Flash.Upload');
        $ret['ownModel'] = 'Vpc_Basic_Flash_Upload_Model';
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
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

    public function hasContent()
    {
        return !!$this->_getUploadUrl();
    }
}