<?php
class Kwf_Assets_Ext4_Extensible_JsDependency extends Kwf_Assets_Dependency_File_Js
{
    protected function _getContents($language, $pack)
    {
        $ret = parent::_getContents($language, $pack);
        $ret = preg_replace("#Ext\.getVersion\(\)\.isLessThan\('4\.2(.0)?'\) \? ('[^']*') : ('[^']*')#", '\3', $ret);
        $ret = "(function(Ext) {\n".$ret."\n})(this.Ext4);\n";
        return $ret;
    }
}
