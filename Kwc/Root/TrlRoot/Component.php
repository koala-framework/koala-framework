<?php
class Vpc_Root_TrlRoot_Component extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['box']);
        $ret['generators']['master'] = array(
            'class' => 'Vpc_Chained_Trl_MasterGenerator',
            'component' => 'Vpc_Root_TrlRoot_Master_Component',
        );
        $ret['generators']['chained'] = array(
            'class' => 'Vpc_Chained_Trl_ChainedGenerator',
            'component' => 'Vpc_Root_TrlRoot_Chained_Component.Vpc_Root_TrlRoot_Master_Component',
            'filenameColumn' => 'filename',
            'nameColumn' => 'name',
            'uniqueFilename' => true,
        );
        $ret['childModel'] = new Vpc_Root_TrlRoot_Model(array('de' => 'Deutsch'));
        return $ret;
    }

    public function getPageByUrl($path, $acceptLanguage)
    {
        if ($path == '') {
            $ret = null;
            $lngs = array();
            foreach ($this->getData()->getChildComponents(array('pseudoPage'=>true)) as $c) {
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
            return $ret->getChildPage(array('home' => true));
        }
        return parent::getPageByUrl($path, $acceptLanguage);
    }
}
