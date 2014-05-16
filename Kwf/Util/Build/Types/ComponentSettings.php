<?php
class Kwf_Util_Build_Types_ComponentSettings extends Kwf_Util_Build_Types_Abstract
{
    protected function _build($options)
    {
        if (!file_exists('build/component')) {
            mkdir('build/component');
        }

        Kwf_Component_Settings::resetSettingsCache();

        $fileName = 'build/component/settings';
        if (file_exists($fileName)) unlink($fileName);
        $data = Kwf_Component_Settings::_getSettingsCached();
        file_put_contents($fileName, serialize($data));
    }

    public function getTypeName()
    {
        return 'componentSettings';
    }
}
