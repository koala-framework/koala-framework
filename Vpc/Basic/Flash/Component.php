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
        if ($this->_getRow()->flash_source_type == 'external_flash_url') {
            $ret['url'] = $this->_getRow()->external_flash_url;
        } else {
            $ret['url'] = $this->_getUploadUrl();
        }
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

    public function hasContent()
    {
        if ($this->_getRow()->flash_source_type == 'external_flash_url'
            && !empty($this->_getRow()->external_flash_url)
        ) {
            return true;
        } else if ($this->_getUploadUrl()) {
            return true;
        }
        return false;
    }
}
