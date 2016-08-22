<?php
class Kwf_Assets_Dependency_Filter_UglifyJs
{
    public static function build($buildFile, $sourceFileUrl)
    {
        $uglifyjs = getcwd()."/".VENDOR_PATH."/bin/node ".getcwd().'/node_modules/uglify-js/bin/uglifyjs';
        $cmd = "$uglifyjs ";
        $cmd .= "--source-map ".escapeshellarg("$buildFile.min.js.map.json").' ';
        $cmd .= "--prefix 2 ";
        $cmd .= "--output ".escapeshellarg("$buildFile.min.js").' ';
        $cmd .= escapeshellarg($buildFile);
        $cmd .= " 2>&1";
        $out = array();
        exec($cmd, $out, $retVal);
        if ($retVal) {
            throw new Kwf_Exception("uglifyjs failed: ".implode("\n", $out));
        }
        $contents = file_get_contents("$buildFile.min.js");
        $contents = str_replace("\n//# sourceMappingURL=$buildFile.min.js.map.json", '', $contents);

        $mapData = json_decode(file_get_contents("$buildFile.min.js.map.json"), true);
        if (count($mapData['sources']) > 1) {
            throw new Kwf_Exception("uglifyjs must not return multiple sources, ".count($mapData['sources'])." returned for '$buildFile'");
        }
        unset($mapData['file']);
        $mapData['sources'][0] = $sourceFileUrl;
        file_put_contents("$buildFile.min.js.map.json", json_encode($mapData));

        $map = new Kwf_SourceMaps_SourceMap(file_get_contents("$buildFile.min.js.map.json"), $contents);
        $mappings = $map->getMappings();
        foreach ($mappings as $k=>$m) {
            $mappings[$k]['originalName'] = null;
        }
        $map->setMappings($mappings);
        $map->save("$buildFile.min.js.map.json", "$buildFile.min.js"); //adds last extension
        return $map;
    }
}
