<?php
class Kwf_Assets_Ext4_Extensible_JsDependency extends Kwf_Assets_Dependency_File_Js
{
    protected function _getRawContents($language)
    {
        $ret = parent::_getRawContents($language);
        $ret = preg_replace("#Ext\.getVersion\(\)\.isLessThan\('4\.2(.0)?'\) \? ('[^']*') : ('[^']*')#", '\3', $ret);
        $ret = "(function(Ext) {\n".$ret."\n})(this.Ext4);\n";
        return $ret;
    }
}
