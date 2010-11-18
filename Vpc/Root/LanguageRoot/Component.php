<?php
class Vpc_Root_LanguageRoot_Component extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['language'] = array(
            'class' => 'Vps_Component_Generator_PseudoPage_Static',
            'component' => array(
                'de'=>'Vpc_Root_LanguageRoot_Language_Component',
                'en'=>'Vpc_Root_LanguageRoot_Language_Component'
            )
        );
        return $ret;
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
