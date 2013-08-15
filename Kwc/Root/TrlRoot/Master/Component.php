<?php
class Kwc_Root_TrlRoot_Master_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['flag'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Root_TrlRoot_Master_FlagImage_Component',
        );
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'switchLanguage' => 'Kwc_Box_SwitchLanguage_Component'
            ),
            'inherit' => true,
            'priority' => 0
        );
        $ret['generators']['category'] = array(
            'class' => 'Kwc_Root_CategoryGenerator',
            'component' => 'Kwc_Root_Category_Component',
            'model' => 'Kwc_Root_CategoryModel'
        );
        $ret['flags']['hasHome'] = true;
        $ret['flags']['subroot'] = true;
        $ret['flags']['chainedType'] = 'Trl';
        $ret['flags']['hasBaseProperties'] = true;
        $ret['baseProperties'] = array('language');
        $ret['editComponents'] = array('flag');
        return $ret;
    }

    public function getBaseProperty($propertyName)
    {
        if ($propertyName == 'language') {
            return $this->getData()->language;
        }
        return null;
    }
}
