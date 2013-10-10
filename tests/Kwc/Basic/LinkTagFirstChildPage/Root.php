<?php
class Kwc_Basic_LinkTagFirstChildPage_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwc_Basic_LinkTagFirstChildPage_PagesModel';
        $ret['generators']['page']['component'] = array(
            'link' => 'Kwc_Basic_LinkTag_FirstChildPage_Component',
            'empty' => 'Kwc_Basic_None_Component'
        );
        $ret['flags']['menuCategory'] = 'root';
        //$ret['generators']['box']['component']['menu'] = 'Kwc_Basic_LinkTagFirstChildPage_Menu_Component';
        $ret['generators']['menu'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_LinkTagFirstChildPage_Menu_Component',
            'inherit' => true,
            'unique' => false
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
