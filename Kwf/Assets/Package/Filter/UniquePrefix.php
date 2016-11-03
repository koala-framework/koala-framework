<?php
class Kwf_Assets_Package_Filter_UniquePrefix
{
    public static function filter($map, $uniquePrefix)
    {
        $head = '
        (function() {
            if (!window.'.$uniquePrefix.') window.'.$uniquePrefix.' = {};
            var kwfUp = window.'.$uniquePrefix.';

            var kwfNamespaces = ["Kwf", "Kwc", "Ext2", "Ext", "$", "jQuery", "Modernizr", "require", "define", "_kwfTrl", "_kwfTrlp"];

            var kwfOrigExports = {};
            for (var i=0; i<kwfNamespaces.length; i++) {
                var name = kwfNamespaces[i];
                kwfOrigExports[name] = window[name];
                if (kwfUp[name]) {
                    window[name] = kwfUp[name];
                } else {
                    window[name] = undefined;
                    try {
                        delete window[name];
                    } catch (e) {
                    }
                }
            }
            if (!window.Ext2) window.Ext2 = {};
        ';

        $foot = '
        for (var i=0; i<kwfNamespaces.length; i++) {
                var name = kwfNamespaces[i];
                try {
                    kwfUp[name] = window[name] || eval(name);
                } catch(e) {
                }
                if (kwfOrigExports[name]) {
                    window[name] = kwfOrigExports[name];
                } else {
                    window[name] = undefined;
                    try {
                        delete window[name];
                    } catch (e) {
                    }
                }
                eval("var "+name+" = kwfUp."+name+";");
            }
        })();
        ';

        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap('');
        $ret->concat(Kwf_SourceMaps_SourceMap::createEmptyMap($head));
        $ret->concat($map);
        $ret->concat(Kwf_SourceMaps_SourceMap::createEmptyMap($foot));

        $mapData = $map->getMapContentsData(false);
        if (isset($mapData->{'_x_org_koala-framework_sourcesContent'})) {
            $retData = $ret->getMapContentsData(false);
            $retData->{'_x_org_koala-framework_sourcesContent'} = $mapData->{'_x_org_koala-framework_sourcesContent'};
        }

        return $ret;
    }
}

