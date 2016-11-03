<?php
class Kwf_Assets_Package_Filter_LoadDeferred
{
    public static function filter($ret)
    {
        $uniquePrefix = Kwf_Config::getValue('application.uniquePrefix');
        if ($uniquePrefix) $uniquePrefix = $uniquePrefix.'.';
        $head = '
        '.$uniquePrefix.'Kwf.loadDeferred(function() {
        ';

        $foot = '
        });
        ';

        $map = Kwf_SourceMaps_SourceMap::createEmptyMap('');
        $map->concat(Kwf_SourceMaps_SourceMap::createEmptyMap($head));
        $map->concat($ret);
        $map->concat(Kwf_SourceMaps_SourceMap::createEmptyMap($foot));
        $ret = $map;

        return $ret;
    }
}

