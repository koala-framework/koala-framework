<?php
class Kwc_Root_TrlRoot_Component extends Kwc_Root_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['box']);
        $ret['generators']['master'] = array(
            'class' => 'Kwc_Chained_Trl_MasterGenerator',
            'component' => 'Kwc_Root_TrlRoot_Master_Component',
        );
        $ret['generators']['chained'] = array(
            'class' => 'Kwc_Chained_Trl_ChainedGenerator',
            'component' => 'Kwc_Root_TrlRoot_Chained_Component.Kwc_Root_TrlRoot_Master_Component',
            'filenameColumn' => 'filename',
            'nameColumn' => 'name',
            'uniqueFilename' => true,
        );
        $ret['childModel'] = new Kwc_Root_TrlRoot_Model(array('de' => 'Deutsch'));
        $ret['flags']['hasAvailableLanguages'] = true;
        return $ret;
    }

    public static function getAvailableLanguages($componentClass)
    {
        $ret = array();
        $rows = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($componentClass, 'childModel'))->getRows();
        foreach ($rows as $row) {
            $ret[] = $row->id;
        }
        return $ret;
    }

    public function getPageByUrl($path, $acceptLanguage)
    {
        return self::getChildPageByPath($this->getData(), $path, $acceptLanguage);
    }

    public static function getChildPageByPath($component, $path, $acceptLanguage)
    {
        if ($path == '') {
            return self::getChildDataByAcceptLanguage($component, $acceptLanguage);
        } else {
            return parent::getChildPageByPath($component, $path);
        }
    }

    public static function getChildDataByAcceptLanguage($component, $acceptLanguage)
    {
        $ret = null;
        $lngs = array();
        foreach ($component->getChildComponents(array('pseudoPage'=>true, 'flag'=>'subroot')) as $c) {
            $lngs[$c->filename] = $c;
        }
        if(preg_match('#^([a-z]{2,3})#', $acceptLanguage, $m)) {
            if (isset($lngs[$m[1]])) {
                $ret = $lngs[$m[1]];
            }
        }
        if (!$ret) {
            $ret = current($lngs);
        }
        if (!$ret) return $ret;
        return $ret->getChildPage(array('home' => true));
    }
}
