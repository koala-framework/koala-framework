<?php
class Vpc_Abstract_Image_Trl_Component extends Vpc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => $masterComponentClass
        );
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['data'] = $ret['chained'];
        $ret['ownImage'] = null;
        if ($this->getRow()->own_image) {
            $ret['ownImage'] = $this->getData()->getChildComponent('-image');
        }
        return $ret;
    }
}
