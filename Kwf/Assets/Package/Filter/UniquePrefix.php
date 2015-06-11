<?php
class Kwf_Assets_Package_Filter_UniquePrefix
{
    public static function filter($map, $uniquePrefix)
    {
        $head = '
        (function() {
            if (!window.'.$uniquePrefix.') window.'.$uniquePrefix.' = {};
            var kwfUp = window.'.$uniquePrefix.';

            var kwfNamespaces = ["Kwf", "Kwc", "Ext2", "$", "jQuery", "Modernizr", "require", "trl", "trlp"];

            var kwfOrigExports = {};
            for (var i=0; i<kwfNamespaces.length; i++) {
                var up = kwfNamespaces[i];
                kwfOrigExports[up] = window[up];
                if (kwfUp[up]) {
                    window[up] = kwfUp[up];
                } else {
                    try {
                        delete window[up];
                    } catch (e) {
                        window[up] = undefined;
                    }
                }
            }
            if (!window.Ext2) window.Ext2 = {};
        ';

        $foot = '
        for (var i=0; i<kwfNamespaces.length; i++) {
                var up = kwfNamespaces[i];
                kwfUp[up] = window[up] || eval(up);
                if (kwfOrigExports[up]) {
                    window[up] = kwfOrigExports[up];
                } else {
                    try {
                        delete window[up];
                    } catch (e) {
                        window[up] = undefined;
                    }
                }
                eval("var "+up+" = kwfUp."+up+";");
            }
        })();
        ';

        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap('');
        $ret->concat(Kwf_SourceMaps_SourceMap::createEmptyMap($head));
        $ret->concat($map);
        $ret->concat(Kwf_SourceMaps_SourceMap::createEmptyMap($foot));

        return $ret;
    }
}

