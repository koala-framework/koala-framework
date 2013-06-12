<?php
class Kwf_Util_ClearCache_Types_AssetsServer extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        $url = Kwf_Config::getValue('assetsCacheUrl').'?web='.Kwf_Config::getValue('application.id').'&section='.Kwf_Setup::getConfigSection().'&clear';
        try {
            $out = file_get_contents($url);
            $this->_output("cleared:     assetsServer [".$out."]\n");
        } catch (Exception $e) {
            $this->_output("cleared:     assetsServer [ERROR] ".$e->getMessage()."\n");
        }
    }

    public function getTypeName()
    {
        return 'assetsServer';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
