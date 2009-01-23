<?php
class Vpc_Root_DomainRoot_Domain_Analytics_Component extends Vpc_Box_Analytics_Component
{
    protected function _getAnalyticsCode()
    {
        $domain = $this->getData()->parent->row->id;
        $s = Vps_Registry::get('config')->statistic;
        if (isset($s->analyticsCode) && isset($s->analyticsCode->$domain)) {
            return $s->analyticsCode->$domain;
        }
        return null;
    }
}
