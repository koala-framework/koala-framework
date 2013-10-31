<?php
class Kwf_Assets_Ext4_CssDependency extends Kwf_Assets_Dependency_File
{
    public function getMimeType()
    {
        return 'text/css';
    }

    public function getContents($language)
    {
        $ret = parent::getContents($language);
        $ret = str_replace('.x-', '.x4-', $ret);
        $ret = str_replace('url(images/', 'url(/assets/ext4/resources/ext-theme-neptune/images/', $ret);
        return $ret;
    }
}
