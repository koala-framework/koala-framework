<?php
abstract class Vpc_Basic_LinkTag_Abstract_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentIcon'] = new Vps_Asset('page_link');
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = array(
            'data' => $this->getData()
        );
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getData()->url) {
            return true;
        } else {
            return false;
        }
    }

}
