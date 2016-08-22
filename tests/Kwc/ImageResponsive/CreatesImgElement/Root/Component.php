<?php
class Kwc_ImageResponsive_CreatesImgElement_Root_Component extends Kwc_Root_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['generators']['imageabstract1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'filename' => 'imageabstract1',
            'name' => 'AbstractImage default (300x200)',
            'component' => 'Kwc_ImageResponsive_CreatesImgElement_Components_ImageAbstract_Component'
        );
        $ret['generators']['imageabstract2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'filename' => 'imageabstract2',
            'name' => 'AbstractImage contentWidth (600x0)',
            'component' => 'Kwc_ImageResponsive_CreatesImgElement_Components_ImageAbstract_Component'
        );
        $ret['generators']['imageabstract3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'filename' => 'imageabstract3',
            'name' => 'AbstractImage original (Original)',
            'component' => 'Kwc_ImageResponsive_CreatesImgElement_Components_ImageAbstract_Component'
        );
        $ret['generators']['imageabstract4'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'filename' => 'imageabstract4',
            'name' => 'AbstractImage custom (400x400)',
            'component' => 'Kwc_ImageResponsive_CreatesImgElement_Components_ImageAbstract_Component'
        );

        $ret['generators']['imagebasic1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'filename' => 'imagebasic1',
            'name' => 'BasicImage',
            'component' => 'Kwc_ImageResponsive_CreatesImgElement_Components_ImageBasic_Component'
        );

        $ret['generators']['imageenlarge1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'filename' => 'imageenlarge1',
            'name' => 'BasicImageEnlarge',
            'component' => 'Kwc_ImageResponsive_CreatesImgElement_Components_ImageEnlarge_Component'
        );

        $ret['generators']['textimage1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'filename' => 'textimage1',
            'name' => 'TextImage',
            'component' => 'Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_Component'
        );
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
