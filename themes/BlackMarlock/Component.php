<?php
class BlackMarlock_Component extends Kwf_Component_Theme_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('BlackMarlock');
        return $ret;
    }

    public static function getRootSettings()
    {
        $ret = array();
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'mainMenu' => 'BlackMarlock_Menu_Main_Component',
                'bottomMenu' => 'BlackMarlock_Menu_Bottom_Component',
                'title' => 'Kwc_Box_TitleEditable_Component'
            ),
            'inherit' => true,
        );
        $ret['generators']['box']['component']['metaTags'] = 'Kwc_Box_MetaTagsContent_Component';
        // $ret['generators']['title']['component'] = 'Kwc_Box_TitleEditable_Component';

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
                        'listFade' => 'BlackMarlock_List_Fade_Component'
                ),
                'inherit' => true,
                'boxName' => 'List Fade'
        );

        $ret['editComponents'] = array('title', 'metaTags', 'openGraph', 'listFade');

        $ret['contentWidth'] = 660;

        $ret['assets']['files'][] = 'kwf/themes/BlackMarlock/Master.scss';
        $ret['assets']['files'][] = 'kwf/themes/BlackMarlock/Web.scss';
        return $ret;
    }
}
