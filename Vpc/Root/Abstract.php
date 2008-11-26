<?php
class Vpc_Root_Abstract extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => array(),
            'inherit' => true,
            'priority' => 0
        );
        $ret['generators']['title'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Box_Title_Component',
            'inherit' => true,
            'priority' => 0
        );
        $ret['componentName'] = 'Root';
        return $ret;
    }

    public function formatPath($parsedUrl)
    {
        return $parsedUrl['path'];
    }
}
