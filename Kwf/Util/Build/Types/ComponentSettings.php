<?php
class Kwf_Util_Build_Types_ComponentSettings extends Kwf_Util_Build_Types_Abstract
{
    protected function _build($options)
    {
        echo "\nsettings...\n";
        if (!file_exists('build/component')) {
            mkdir('build/component');
        }

        if (!file_exists('temp/component-assets-build')) {
            mkdir('temp/component-assets-build');
        }

        Kwf_Component_Settings::resetSettingsCache();

        foreach (glob('build/component/*') as $f) {
            unlink($f);
        }

        $fileName = 'build/component/settings';

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
        $componentClasses = Kwc_Abstract::getComponentClasses();

        echo "masterLayouts...\n";
        Kwf_Component_MasterLayout_Abstract::_buildAll($componentClasses);


        echo "layouts...\n";
        Kwf_Component_Layout_Abstract::_buildAll($componentClasses);

        echo "component-assets...\n";
        Kwf_Component_Assets::build();
    }

    private function _checkSettings($settingName, $settings)
    {
        /*
        Check disabled to improve build performance
        if (is_string($settings)) {
            if (isset($settings[0]) && $settings[0] == '/' && substr($settings, 0, 8) != '/assets/') {
                throw new Kwf_Exception("Setting $settingName does look like an absolute path: '$settings' which must not be part of built settings");
            }
        } else if (is_array($settings)) {
            foreach ($settings as $k=>$i) {
                self::_checkSettings($settingName.'.'.$k, $i);
            }
        }
        */
    }

    public function getTypeName()
    {
        return 'componentSettings';
    }
}
