<?php
class Vpc_Basic_Flash_Component extends Vpc_Abstract_Flash_Upload_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Flash_Model';
        return $ret;
    }

    protected function _getFlashData()
    {
        $ret = parent::_getFlashData();
        $ret['url'] = $this->_getUploadUrl();
        $ret['width'] = $this->_getRow()->width;
        $ret['height'] = $this->_getRow()->height;
        return $ret;
    }

    protected function _getFlashVars()
    {
        $ret = parent::_getFlashVars();
        $vars = $this->_getRow()->getChildRows('FlashVars');
        if (count($vars)) {
            foreach ($vars as $var) {
                if (!empty($var->key)) {
                    $ret[$var->key] = $var->value;
                }
            }
        }
        return $ret;
    }
}
