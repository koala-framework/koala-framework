<?php
class Vpc_Shop_Cart_Detail_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Shop_Cart_Detail_Form_Component';
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['product'] = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->getData()->row->add_component_id)
            ->parent;
        return $ret;
    }

}
