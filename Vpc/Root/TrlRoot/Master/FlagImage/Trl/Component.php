<?php
class Vpc_Root_TrlRoot_Master_FlagImage_Trl_Component extends Vpc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => $masterComponentClass,
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['image'] = $this->getData()->getChildComponent('-image');
        return $ret;
    }

    public function hasContent()
    {
        $ret = parent::hasContent();
        if ($ret) return $ret;

        return $this->getData()->getChildComponent('-image')->hasContent();
    }
}
