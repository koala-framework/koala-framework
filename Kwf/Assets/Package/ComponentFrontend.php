<?php
class Kwf_Assets_Package_ComponentFrontend extends Kwf_Assets_Package_Default
{
    static $_instance;

    public static function getInstance($dependencyName = null)
    {
        if ($dependencyName) throw new Kwf_Exception("Parameter must be null");
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        parent::__construct('Frontend');
    }

    public function toUrlParameter()
    {
        return 'Frontend';
    }

    public static function fromUrlParameter($class, $parameter)
    {
        return self::getInstance();
    }


    public function getPackageContents($mimeType, $language, $includeSourceMapComment = true)
    {
        $ret = parent::getPackageContents($mimeType, $language, $includeSourceMapComment);
        if ($mimeType == 'text/javascript; defer') {
            $uniquePrefix = Kwf_Config::getValue('application.uniquePrefix');
            if ($uniquePrefix) $uniquePrefix = $uniquePrefix.'.';

            $foot = '
            if ('.$uniquePrefix.'Kwf._loadDeferred) '.$uniquePrefix.'Kwf._loadDeferred.forEach(function(i) { i(); });
            '.$uniquePrefix.'Kwf._loadDeferred = "done";
            ';

            $map = Kwf_SourceMaps_SourceMap::createEmptyMap('');
            $map->concat($ret);
            $map->concat(Kwf_SourceMaps_SourceMap::createEmptyMap($foot));
            $ret = $map;
        }


        return $ret;
    }
}
