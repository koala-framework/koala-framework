<?php
class Vpc_Basic_Feed_Feed extends Vpc_Abstract_Feed_Component
{
    protected function _getRssEntries()
    {
        return Vps_Model_Abstract::getInstance('Vpc_Basic_Feed_Model')
            ->getRows()->toArray();
    }

    public static function getStaticCacheMeta()
    {
        $ret = parent::getStaticCacheMeta();
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vpc_Basic_Feed_Model');
        return $ret;
    }
}
