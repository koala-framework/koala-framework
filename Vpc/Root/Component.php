<?php
class Vpc_Root_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vps_Component_Generator_Page',
            'component' => array(
                'paragraphs' => 'Vpc_Paragraphs_Component',
                'link' => 'Vpc_Basic_LinkTag_Component',
                'firstChildPage' => 'Vpc_Basic_LinkTag_FirstChildPage_Component'
            )
        );
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => array(),
            'inherit' => true,
            'priority' => 0
        );
        return $ret;
    }
}
