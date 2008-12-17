<?php
class Vpc_Posts_Detail_Signature_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['user'] = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Vpc_User_Directory_Component',
                array('subroot' => $this->getData())
            )
            ->getChildComponent('_'.$this->getData()->parent->row->user_id);
        return $ret;
    }
}
