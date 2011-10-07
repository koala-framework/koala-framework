<?php
class Kwc_Root_LanguageRoot_Language_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category'] = array(
            'class' => 'Kwc_Root_CategoryGenerator',
            'component' => 'Kwc_Root_Category_Component',
            'model' => 'Kwc_Root_CategoryModel'
        );
        $ret['componentName'] = trlKwf('Language');
        $ret['flags']['subroot'] = 'language';
        $ret['flags']['hasHome'] = true;
        $ret['flags']['hasLanguage'] = true;
        return $ret;
    }

    public function getLanguage()
    {
        return $this->getData()->id;
    }
}
