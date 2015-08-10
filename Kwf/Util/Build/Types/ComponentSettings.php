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
        try {
            $data = Kwf_Component_Settings::_getSettingsCached();
        } catch (Kwf_Trl_BuildFileMissingException $e) {
            $originatingException = $e->getSettingsNonStaticTrlException();
            if ($originatingException) {
                throw $originatingException;
            }
            throw $e;
        }
        foreach ($data as $cmp=>$settings) {
            self::_checkSettings($cmp, $settings);
        }
        file_put_contents($fileName, serialize($data));
    }

    private function _checkSettings($settingName, $settings)
    {
        if (is_string($settings)) {
            if (isset($settings[0]) && $settings[0] == '/' && substr($settings, 0, 8) != '/assets/') {
                throw new Kwf_Exception("Setting $settingName does look like an absolute path: '$settings' which must not be part of built settings");
            }
        } else if (is_array($settings)) {
            foreach ($settings as $k=>$i) {
                self::_checkSettings($settingName.'.'.$k, $i);
            }
        }
    }

    public function getTypeName()
    {
        return 'componentSettings';
    }
}
