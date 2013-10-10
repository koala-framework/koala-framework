<?php
class RedMallee_Component extends Kwf_Component_Theme_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'RedMallee';
        return $ret;
    }

    public static function getRootSettings()
    {
        $ret = array();
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'mainMenu' => 'RedMallee_Menu_Main_Component',
                'verticalMenu' => 'RedMallee_Menu_MainVertical_Component',
                'subMenu' => 'RedMallee_Menu_Sub_Component',
                'subMenuHorizontal' => 'RedMallee_Menu_SubHorizontal_Component',
                'subSubMenuHorizontal' => 'RedMallee_Menu_SubSubHorizontal_Component',
                'subSubMenu' => 'RedMallee_Menu_SubSub_Component',
                'bottomMenu' => 'RedMallee_Menu_Bottom_Component',
                'topMenu' => 'RedMallee_Menu_Top_Component',
            ),
            'inherit' => true,
        );
        $ret['generators']['logo'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'logo' => 'RedMallee_Box_Logo_Component'
            ),
            'inherit' => true,
            'unique' => true
        );
        
        $ret['generators']['footerLogos'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'RedMallee_Box_FooterLogos_Component',
            'inherit' => true,
            'unique' => true
        );
        
        $ret['generators']['box']['component']['metaTags'] = 'Kwc_Box_MetaTagsContent_Component';
        $ret['generators']['title'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Box_TitleEditable_Component',
            'inherit' => true,
        );
        $ret['generators']['breadcrumbs'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'RedMallee_Breadcrumbs_Component',
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
                        'listFade' => 'RedMallee_List_Fade_Component'
                ),
                'inherit' => true,
                'boxName' => 'List Fade'
        );
        $ret['generators']['searchBox'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'RedMallee_FulltextSearch_Box_Component',
            'unique' => true,
            'inherit' => true
        );
        $ret['generators']['rightBox'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'unique' => true,
            'inherit' => true
        );

        $ret['generators']['search'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'RedMallee_FulltextSearch_Search_Directory_Component',
            'name' => trlStatic('Suche')
        );

        $ret['generators']['background'] = array(
            'class' => 'Kwf_Component_Generator_Box_StaticSelect',
            'component' => array(
                'parentContent' => 'Kwc_Basic_ParentContent_Component',
                'background'        => 'RedMallee_Box_BackgroundImage_Component',
            ),
            'inherit' => true,
            'boxName' => trlStatic('Hintergrundbild')
        );
        
        $ret['editComponents'] = array('title', 'metaTags', 'listFade', 'logo', 'background', 'footerLogos');

        $ret['masterTemplate'] = KWF_PATH.'/themes/RedMallee/Master.tpl';

        $ret['assets']['files'][] = 'kwf/themes/RedMallee/Master.scss';
        $ret['assets']['files'][] = 'kwf/themes/RedMallee/Web.scss';
        $ret['assets']['files'][] = 'kwf/themes/RedMallee/js/stickyHeader.js';
        $ret['assets']['dep'][] = 'KwfOnReady';
        $ret['assets']['dep'][] = 'jQuery';

        return $ret;
    }
}
