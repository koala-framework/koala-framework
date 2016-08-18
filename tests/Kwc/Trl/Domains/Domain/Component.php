<?php
class Kwc_Trl_Domains_Domain_Component extends Kwc_Root_DomainRoot_Domain_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['category']);
        $ret['baseProperties'] = array('domain');
        $ret['flags']['hasHome'] = false;

        $ret['generators']['master'] = array(
            'class' => 'Kwc_Trl_Domains_Domain_MasterGenerator',
            'component' => 'Kwc_Trl_Domains_Domain_Master_Component',
        );
        $ret['generators']['chained'] = array(
            'class' => 'Kwc_Trl_Domains_Domain_ChainedGenerator',
            'component' => 'Kwc_Root_TrlRoot_Chained_Component.Kwc_Trl_Domains_Domain_Master_Component',
            'filenameColumn' => 'filename',
            'nameColumn' => 'name',
            'uniqueFilename' => true,
        );

        $ret['childModel'] = 'Kwc_Trl_Domains_Domain_Model';
        return $ret;
    }

    public function getPageByUrl($path, $acceptLanguage)
    {
        return Kwc_Root_TrlRoot_Component::getChildPageByPath($this->getData(), $path, $acceptLanguage);
    }
}
