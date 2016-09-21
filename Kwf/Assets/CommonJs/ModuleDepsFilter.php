<?php
class Kwf_Assets_CommonJs_ModuleDepsFilter implements Kwf_Assets_Dependency_Filter_Interface
{
    public function filter(Kwf_SourceMaps_SourceMap $sourcemap)
    {
        $temp = tempnam('temp/', 'commonjs');
        file_put_contents($temp, $sourcemap->getFileContents());
        $parsedFile = Kwf_Assets_CommonJs_ModuleDepsParser::parse($temp);
        unlink($temp);

        $newSourceMap = Kwf_SourceMaps_SourceMap::createEmptyMap($parsedFile['source']);
        if (isset($sourcemap->getMapContentsData(false)->{'_x_org_koala-framework_trlData'})) {
            $newSourceMap->getMapContentsData(false)->{'_x_org_koala-framework_trlData'} = $sourcemap->getMapContentsData(false)->{'_x_org_koala-framework_trlData'};
        }
        if (isset($sourcemap->getMapContentsData(false)->{'_x_org_koala-framework_masterFiles'})) {
            $newSourceMap->getMapContentsData(false)->{'_x_org_koala-framework_masterFiles'} = $sourcemap->getMapContentsData(false)->{'_x_org_koala-framework_masterFiles'};
        }
        return $newSourceMap;
    }
}
