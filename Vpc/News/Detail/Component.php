<?php
class Vpc_News_Detail_Component extends Vpc_News_Detail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['image'] = 'Vpc_News_Detail_PreviewImage_Component';
        return $ret;
    }
}
