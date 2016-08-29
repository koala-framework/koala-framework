<?php
class Kwf_Component_Cache_Paging_Directory_View_Paging_Component extends Kwc_Paging_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['pagesize'] = 2;
        $ret['placeholder'] = array(
            'first'    => 'f',
            'previous' => 'p',
            'next'     => 'n',
            'last'     => 'l',
            'prefix'   => ''
        );
        return $ret;
    }
}
