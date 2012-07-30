<?php
class Vpc_Statistics_Piwik_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['domain'] = $this->_getDomain();
        $ret['id'] = $this->_getIdSite();
        return $ret;
    }

    public function hasContent()
    {
        return !!$this->_getIdSite();
    }

    protected function _getDomain()
    {
        return Vps_Registry::get('config')->statistic->piwikDomain;
    }

    protected function _getIdSite()
    {
        $cfg = Vps_Registry::get('config');
        $ret = $cfg->statistic->piwikId;
        $ignore = $cfg->statistic->ignore;

        $domain = $this->getData();
        while ($domain && !is_instance_of(
            $domain->componentClass, 'Vpc_Root_DomainRoot_Domain_Component'
        )) {
            $domain = $domain->parent;
        }
        if ($domain) {
            $domains = $cfg->vpc->domains;
            $domain = $domains->{$domain->id};
            if (isset($domain->piwikId)) $ret = $domain->piwikId;
            if (isset($domain->statistik->ignore)) $ignore = $domain->statistik->ignore;
        }
        if (!$ignore) {
            return $ret;
        }
    }
}
