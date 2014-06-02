<?php
class Kwf_Assets_Ext4_JsDependency extends Kwf_Assets_Dependency_File_Js
{
    public function _getRawContents($language)
    {
        $ret = parent::_getRawContents($language);
        if ($this->getFileNameWithType() == 'ext4/src/Ext.js') {
            $ret = "Ext.sandboxName = 'Ext4';\n".$ret;
            $ret = "Ext.isSandboxed = true;\n".$ret;
            $ret = "Ext.buildSettings = { baseCSSPrefix: \"x4-\", scopeResetCSS: true };\n".$ret;
        } else if ($this->getFileNameWithType() == 'ext4/src/class/Loader.js') {
            $ret .= "\n";
            $ret .= "Ext.Loader.setConfig({\n";
            $ret .= "    enabled: true,\n";
            $ret .= "    disableCaching: false,\n";
            $ret .= "    paths: {\n";
            $ret .= "        'Ext': '/assets/ext4/src'\n";
            $ret .= "    }\n";
            $ret .= "});\n";
        }
        $ret = "(function(Ext) {".$ret."})(this.Ext4 || (this.Ext4 = {}));";
        return $ret;
    }
}
