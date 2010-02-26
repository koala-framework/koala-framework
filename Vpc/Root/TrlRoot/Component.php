<?php
class Vpc_Root_TrlRoot_Component extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['master'] = array(
            'class' => 'Vpc_Root_TrlRoot_Generator',
            'component' => 'Vpc_Root_TrlRoot_Master_Component',
            'name' => 'de',
        );
        $ret['generators']['chained'] = array(
            'class' => 'Vpc_Root_TrlRoot_Generator',
            'component' => array(
                'en' => 'Vpc_Chained_Trl_Base_Component.Vpc_Root_TrlRoot_Master_Component',
            )
        );
        return $ret;
    }

    public function getPageByUrl($path, $acceptLangauge)
    {
        if ($path == '') {
            $ret = null;
            $lngs = array();
            foreach ($this->getData()->getChildComponents(array('pseudoPage'=>true)) as $c) {
                $lngs[$c->filename] = $c;
            }
            if(preg_match('#^([a-z]{2,3})#', $acceptLangauge, $m)) {
                if (isset($lngs[$m[1]])) {
                    $ret = $lngs[$m[1]];
                }
            }
            if (!$ret) {
                $ret = current($lngs);
            }
            return $ret->getChildPage(array('home' => true));
        }
        return parent::getPageByUrl($path, $acceptLangauge);
    }
}
