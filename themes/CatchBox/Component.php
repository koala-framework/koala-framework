<?php
class CatchBox_Component extends Kwf_Component_Theme_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Catch Box');
        return $ret;
    }

    public static function getRootSettings()
    {
        $ret = array();
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'mainMenu' => 'CatchBox_Menu_Main_Component',
                'subMenu' => 'CatchBox_Menu_Sub_Component',
                'bottomMenu' => 'CatchBox_Menu_Bottom_Component',
            ),
            'inherit' => true,
        );
        $ret['generators']['headerTitle'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'CatchBox_Box_HeaderTitle_Component',
            'inherit' => true,
            'unique' => true,
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

        $ret['generators']['searchBox'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'CatchBox_FulltextSearch_Box_Component',
            'unique' => true,
            'inherit' => true
        );

        $ret['generators']['search'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'CatchBox_FulltextSearch_Search_Directory_Component',
            'name' => trlStatic('Suche')
        );

        $ret['editComponents'] = array('title', 'metaTags', 'openGraph');

        $ret['contentWidth'] = 800;
        $ret['contentWidthBoxSubtract'] = array(
            'subMenu' => 200
        );

        $ret['assets']['files'][] = 'kwf/themes/CatchBox/css/master.css';
        $ret['assets']['files'][] = 'kwf/themes/CatchBox/css/web.scss';

        return $ret;
    }
}
