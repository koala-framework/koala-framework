<?php
class Kwf_Assets_Dispatcher
{
    static public function dispatch($url)
    {
        if (substr($url, 0, 21)=='/assets/dependencies/') {
            $out = self::getOutputForUrl($url);
            Kwf_Media_Output::output($out);
        }
    }

    static public function getOutputForUrl($url)
    {
        if (substr($url, 0, 21) != '/assets/dependencies/') throw new Kwf_Exception("invalid url: '$url'");
        $url = substr($url, 21);
        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        $param = explode('/', $url);
        $dependencyClass = $param[0];
        $dependencyParams = $param[1];
        $language = $param[2];
        $extension = $param[3];
        if (!is_instance_of($dependencyClass, 'Kwf_Assets_Dependency_UrlResolvableInterface')) {
            throw new Kwf_Exception("invalid dependency class");
        }
        $dependency = call_user_func(array($dependencyClass, 'fromUrlParameter'), $dependencyClass, $dependencyParams);

        if ($extension == 'js') $mimeType = 'text/javascript';
        else if ($extension == 'css') $mimeType = 'text/css';
        else if ($extension == 'printcss') $mimeType = 'text/css; media=print';
        else throw new Kwf_Exception_NotYetImplemented();
        if ($dependency instanceof Kwf_Assets_Dependency_Package) {
            $contents = $dependency->getPackageContents($mimeType, $language);
        } else {
            $contents = $dependency->getContents($language);
        }

        if ($extension == 'js') $mimeType = 'text/javascript; charset=utf-8';
        else if ($extension == 'css' || $extension == 'printcss') $mimeType = 'text/css; charset=utf8';
        return array(
            'contents' => $contents,
            'mimeType' => $mimeType
        );
    }
}
