<?php
class Vpc_Events_Directory_Admin extends Vpc_News_Directory_Admin
{
    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['items']['idTemplate'] = 'events_{0}-content';
        return $ret;
    }
}
