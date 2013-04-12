<?php
class TestTheme_Component extends Kwf_Component_Theme_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Test Theme');
        return $ret;
    }

    public static function getRootSettings()
    {
        $ret = array();
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'mainMenu' => 'TestTheme_Menu_Main_Component',
                'subMenu' => 'TestTheme_Menu_Sub_Component',
                'bottomMenu' => 'TestTheme_Menu_Bottom_Component',
            ),
            'inherit' => true,
        );
        $ret['generators']['metaTags'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Box_MetaTagsContent_Component',
            'inherit' => true,
        );
        $ret['generators']['openGraph'] = array(
            'class' => 'Kwf_Component_Generator_Box_StaticSelect',
            'component' => array(
                    'parentContent' => 'Kwc_Basic_ParentContent_Component',
                    'openGraph' => 'Kwc_Box_OpenGraph_Component'
            ),
            'inherit' => true,
            'boxName' => 'Open Graph'
        );
        $ret['generators']['title'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Box_TitleEditable_Component',
            'inherit' => true,
        );

        $ret['editComponents'] = array('title', 'metaTags', 'openGraph');

        $ret['contentWidth'] = 800;
        $ret['contentWidthBoxSubtract'] = array(
            'subMenu' => 200
        );

        $ret['assets']['files'][] = 'kwf/themes/TestTheme/css/*';

        return $ret;
    }
}
