<?php
class Vpc_Root_TrlRoot_Master_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['flag'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Root_TrlRoot_Master_FlagImage_Component',
        );
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => array(
                'switchLanguage' => 'Vpc_Box_SwitchLanguage_Component'
            ),
            'inherit' => true,
            'priority' => 0
        );
        $ret['generators']['category'] = array(
            'class' => 'Vpc_Root_CategoryGenerator',
            'component' => 'Vpc_Root_Category_Component',
            'model' => 'Vpc_Root_CategoryModel'
        );
        $ret['flags']['hasHome'] = true;
        $ret['flags']['hasLanguage'] = true;
        $ret['flags']['subroot'] = true;
        $ret['flags']['chainedType'] = 'Trl';
        $ret['editComponents'] = array('flag');
        return $ret;
    }

    public function getLanguage()
    {
        return $this->getData()->language;
    }
}
