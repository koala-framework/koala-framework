<?php
class Kwc_Root_LanguageRoot_Component extends Kwc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['language'] = array(
            'class' => 'Kwc_Root_LanguageRoot_Generator',
            'component' => array(
                'de'=>'Kwc_Root_LanguageRoot_Language_Component',
                'en'=>'Kwc_Root_LanguageRoot_Language_Component'
            )
        );
        $ret['flags']['hasAvailableLanguages'] = true;
        return $ret;
    }

    public static function getAvailableLanguages($componentClass)
    {
        $g = Kwc_Abstract::getSetting($componentClass, 'generators');
        return array_keys($g['language']['component']);
    }

    public function getPageByUrl($path, $acceptLangauge)
    {
        if ($path == '') {
            $ret = null;
            $lngs = array();
            foreach ($this->getData()->getChildComponents(array('generator' => 'language')) as $c) {
                $lngs[$c->id] = $c;
            }
            if(preg_match('#^([a-z]{2,3})#', $acceptLangauge, $m)) {
                if (isset($lngs[$m[1]])) {
                    $ret = $lngs[$m[1]];
                }
            }
            if (!$ret) $ret = current($lngs);
            return $ret->getChildPage(array('home' => true));
        }
        return parent::getPageByUrl($path, $acceptLangauge);
    }
}
