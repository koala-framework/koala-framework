<?php
class Vpc_Root_LanguageRoot_Language_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category'] = array(
            'class' => 'Vpc_Root_CategoryGenerator',
            'component' => 'Vpc_Root_Category_Component',
            'model' => 'Vpc_Root_CategoryModel'
        );
        $ret['componentName'] = trlVps('Language');
        $ret['flags']['subroot'] = 'language';
        $ret['flags']['showInPageTreeAdmin'] = true;
        $ret['flags']['hasHome'] = true;
        $ret['flags']['hasLanguage'] = true;
        return $ret;
    }

    public function getLanguage()
    {
        return $this->getData()->id;
    }
}
