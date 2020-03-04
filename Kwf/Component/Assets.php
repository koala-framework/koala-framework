<?php
class Kwf_Component_Assets
{
    protected static function _getKwcClass($class, $master)
    {
        $kwcClass = Kwf_Component_Abstract::formatRootElementClass($class, '');
        if ($master) $kwcClass .= 'Master';
        if (Kwf_Config::getValue('application.uniquePrefix')) {
            $kwcClass = str_replace('kwfUp-', Kwf_Config::getValue('application.uniquePrefix').'-', $kwcClass);
        } else {
            $kwcClass = str_replace('kwfUp-', '', $kwcClass);
        }
        return $kwcClass;
    }

    public static function build()
    {
        $componentFiles = array();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            $componentFiles[$class] = array(
                'files' => array(),
                'kwcClass' => self::_getKwcClass($class, false),
                'kwcClassMaster' => self::_getKwcClass($class, true),
                'assets' => Kwc_Abstract::getSetting($class, 'assets'),
                'assetsDefer' => Kwc_Abstract::getSetting($class, 'assetsDefer'),
                'assetsAdmin' => Kwc_Abstract::getSetting($class, 'assetsAdmin'),
            );
            $files = Kwc_Abstract::getSetting($class, 'componentFiles');

            // array_reverse because assets must be loaded in correct order
            foreach (array_reverse(array_merge($files['masterCss'], $files['css'], $files['js'])) as $f) {
                $componentFiles[$class]['files'][] = $f;
            }
        }

        // Output Assets for root-component and components in assets.componentPackages (the former does not include assets from latter packages)
        $packageClasses = Kwf_Config::getValueArray('assets.componentPackages');
        $packageClasses[] = Kwf_Config::getValue('kwc.rootComponent');
        file_put_contents('temp/component-assets-build/package-classes.json', json_encode($packageClasses));
        $processedClasses = array();
        foreach ($packageClasses as $packageClass) {
            $out = array();
            $classes = Kwf_Component_Settings::getComponentClassesOfStartingClass($packageClass);
            foreach ($classes as $class) {
                if (!in_array($class, $processedClasses)) {
                    $out[$class] = $componentFiles[$class];
                }
            }
            file_put_contents('temp/component-assets-build/assets-' . $packageClass . '.json', json_encode($out));
            $processedClasses = array_merge($processedClasses, $classes);
        }

        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            $config = Kwc_Admin::getInstance($class)->getScssConfig();
            $masterFiles = Kwc_Admin::getInstance($class)->getScssConfigMasterFiles();
            if ($config || $masterFiles) {
                file_put_contents('temp/component-assets-build/scss-config-'.$class.'.json', json_encode(array(
                    'config' => $config,
                    'masterFiles' => $masterFiles
                )));
            }
        }
    }
}

