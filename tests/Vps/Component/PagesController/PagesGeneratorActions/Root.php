<?php
class Vps_Component_PagesController_PagesGeneratorActions_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vpc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'empty' => 'Vpc_Basic_Empty_Component',
                'special' => 'Vps_Component_PagesController_PagesGeneratorActions_SpecialComponent',
                'specialContainer' => 'Vps_Component_PagesController_PagesGeneratorActions_SpecialContainerComponent',
            ),
            'model' => 'Vps_Component_PagesController_PagesGeneratorActions_PagesModel'
        );
        return $ret;
    }
}
