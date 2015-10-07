<?php
class Kwf_Component_Generator_StaticSelectChildTablePage_Root_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['banner'] = array(
            'class' => 'Kwf_Component_Generator_Box_StaticSelect',
            'component' => array(
                'banner1' => 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner1_Component',
                'banner2' => 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner2_Component',
            ),
            'inherit' => true,
            'model' => new Kwf_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data' => array(
                    array('component_id' => 'root_page1-banner', 'component' => 'banner1'),
                    array('component_id' => 'root_page2-banner', 'component' => 'banner2'),
                )
            ))
        );
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_StaticSelectChildTablePage_Page1_Component',
        );
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_StaticSelectChildTablePage_Page2_Component',
        );
        return $ret;
    }
}
