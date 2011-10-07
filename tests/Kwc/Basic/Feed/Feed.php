<?php
class Kwc_Basic_Feed_Feed extends Kwc_Abstract_Feed_Component
{
    protected function _getRssEntries()
    {
        return Kwf_Model_Abstract::getInstance('Kwc_Basic_Feed_Model')
            ->getRows()->toArray();
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwc_Basic_Feed_Model');
        return $ret;
    }
}
