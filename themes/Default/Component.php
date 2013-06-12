<?php
class Default_Component extends Kwf_Component_Theme_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Default');
        return $ret;
    }

    public static function getRootSettings()
    {
        $ret = array();
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'mainMenu' => 'Default_Menu_Main_Component',
                'subMenu' => 'Default_Menu_Sub_Component',
                'bottomMenu' => 'Default_Menu_Bottom_Component',
            ),
            'inherit' => true,
        );
        $ret['generators']['box']['component']['metaTags'] = 'Kwc_Box_MetaTagsContent_Component';
        $ret['generators']['title'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Box_TitleEditable_Component',
            'inherit' => true,
        );
        $ret['generators']['breadcrumbs'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Default_Breadcrumbs_Component',
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
        $ret['generators']['listFade'] = array(
                'class' => 'Kwf_Component_Generator_Box_StaticSelect',
                'component' => array(
                        'parentContent' => 'Kwc_Basic_ParentContent_Component',
                        'listFade' => 'Default_List_Fade_Component'
                ),
                'inherit' => true,
                'boxName' => 'List Fade'
        );
        $ret['editComponents'] = array('title', 'metaTags', 'listFade');

        $ret['contentWidth'] = 480;

        $ret['assets']['files'][] = 'kwf/themes/Default/css/master.css';
        $ret['assets']['files'][] = 'kwf/themes/Default/css/web.css';

        return $ret;
    }
}
