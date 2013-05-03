<?php
require_once Kwf_Config::getValue('externLibraryPath.sass').'/SassParser.php';
class Kwf_Util_SassParser extends SassParser
{
    protected static function _getExtensionPaths()
    {
        return array(
            'sass' => Kwf_Config::getValue('externLibraryPath.sass') . '/Extensions/',
            'kwf' => Kwf_Config::getValue('path.kwf').'/sass/'
        );
    }

    public static function loadCallback($file, $parser)
    {
        $paths = array();
        foreach ($parser->extensions as $extensionName) {
            $namespace = ucwords(preg_replace('/[^0-9a-z]+/', '_', strtolower($extensionName)));
            $extensionPaths = self::_getExtensionPaths();
            foreach($extensionPaths as $key => $extensionPath) {
                if ($key=='sass' && $namespace=='Compass') continue;
                $extensionPath = $extensionPath . $namespace . '/' . $namespace . '.php';
                if (file_exists($extensionPath)) {
                    require_once($extensionPath);
                    if ($namespace == 'Kwf' || $namespace == 'Compass') {
                        $hook = $namespace . 'Sass::resolveExtensionPath';
                    } else {
                        $hook = $namespace . '::resolveExtensionPath';
                    }
                    $returnPath = call_user_func($hook, $file, $parser);
                    if (!empty($returnPath)) {
                        $paths[] = $returnPath;
                    }

                }
            }
        }
        return $paths;
    }

    public static function getExtensionsFunctions($extensions)
    {
        $output = array();
        if (!empty($extensions)) {
            foreach ($extensions as $extension) {
                $name = explode('/', $extension, 2);
                $namespace = ucwords(preg_replace('/[^0-9a-z]+/', '_', strtolower(array_shift($name))));
                $extensionPaths = self::_getExtensionPaths();
                foreach ($extensionPaths as $key => $extensionPath) {
                    if ($key=='sass' && $namespace=='Compass') continue;
                    $extensionPath = $extensionPath . $namespace . '/' . $namespace . '.php';
                    if (file_exists(
                        $extensionPath
                    )
                    ) {
                        require_once($extensionPath);
                        if ($namespace == 'Kwf' || $namespace == 'Compass') {
                            $namespace = $namespace . 'Sass::';
                        } else {
                            $namespace = $namespace . '::';
                        }
                        $function = 'getFunctions';
                        $output = array_merge($output, call_user_func($namespace . $function, $namespace));
                    }
                }
            }
        }
        return $output;
    }
}
