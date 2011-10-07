<?php
class Kwf_Component_PagesController_PagesGeneratorActions_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'empty' => 'Kwc_Basic_Empty_Component',
                'special' => 'Kwf_Component_PagesController_PagesGeneratorActions_SpecialComponent',
                'specialContainer' => 'Kwf_Component_PagesController_PagesGeneratorActions_SpecialContainerComponent',
                'specialWithoutEdit' => 'Kwf_Component_PagesController_PagesGeneratorActions_SpecialWithoutEditContainerComponent',
            ),
            'model' => 'Kwf_Component_PagesController_PagesGeneratorActions_PagesModel'
        );
        return $ret;
    }
}
