<?php
require_once Kwf_Config::getValue('externLibraryPath.sass').'/SassParser.php';
class Kwf_Util_SassParser extends SassParser
{
    protected static function _getExtensionPath()
    {
        return Kwf_Config::getValue('externLibraryPath.sass') . '/Extensions/';
    }

    public static function loadCallback($file, $parser)
    {
        $paths = array();
        foreach ($parser->extensions as $extensionName) {
            $namespace = ucwords(preg_replace('/[^0-9a-z]+/', '_', strtolower($extensionName)));
            $extensionPath = self::_getExtensionPath() . $namespace . '/' . $namespace . '.php';
            if (file_exists($extensionPath)) {
                require_once($extensionPath);
                $hook = $namespace . '::resolveExtensionPath';
                $returnPath = call_user_func($hook, $file, $parser);
                if (!empty($returnPath)) {
                    $paths[] = $returnPath;
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
                $extensionPath = self::_getExtensionPath() . $namespace . '/' . $namespace . '.php';
                if (file_exists(
                    $extensionPath
                )
                ) {
                    require_once($extensionPath);
                    $namespace = $namespace . '::';
                    $function = 'getFunctions';
                    $output = array_merge($output, call_user_func($namespace . $function, $namespace));
                }
            }
        }

        return $output;
    }
}
