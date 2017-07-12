<?php
class Kwf_Assets_Components_Dependency_Js extends Kwf_Assets_Components_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function isCommonJsEntry()
    {
        return true;
    }

    public function getContentsPacked()
    {
        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap('');
        $trlData = array();
        $masterFiles = array();
        foreach ($this->_componentDependencies as $dep) {
            $c = $dep->getContentsPacked();
            $data = $c->getMapContentsData(false);
            if (isset($data->{'_x_org_koala-framework_trlData'})) {
                $trlData = array_merge($trlData, $data->{'_x_org_koala-framework_trlData'});
            }
            if (isset($data->{'_x_org_koala-framework_masterFiles'})) {
                $masterFiles = array_merge($masterFiles, $data->{'_x_org_koala-framework_masterFiles'});
            }

            $sourcesCount = 0;
            $packageData = $ret->getMapContentsData(false);
            if (isset($packageData->sources)) {
                $sourcesCount = count($packageData->sources);
            }
            unset($packageData);

            if (Kwf_Config::getValue('application.uniquePrefix')) {
                $c->stringReplace('kwcBem--', $this->_getKwcClass().'--');
                $c->stringReplace('kwcBem__', $this->_getKwcClass().'__');
            } else {
                $c->stringReplace('kwcBem--', '');
                $c->stringReplace('kwcBem__', '');
            }
            $c->stringReplace('kwcClass', $this->_getKwcClass());

            $ret->concat($c);

            //copy sourcesContent to packageMap with $sourcesCount offset
            $packageData = $ret->getMapContentsData(false);
            if (isset($data->{'_x_org_koala-framework_sourcesContent'})) {
                if (!isset($packageData->{'_x_org_koala-framework_sourcesContent'})) {
                    $packageData->{'_x_org_koala-framework_sourcesContent'} = array();
                }
                foreach ($data->{'_x_org_koala-framework_sourcesContent'} as $k=>$i) {
                    $packageData->{'_x_org_koala-framework_sourcesContent'}[$k+$sourcesCount] = $i;
                }
            }
            unset($packageData);
        }
        $ret->getMapContentsData(false)->{'_x_org_koala-framework_trlData'} = $trlData;
        $ret->getMapContentsData(false)->{'_x_org_koala-framework_masterFiles'} = $masterFiles;

        $ret->setMimeType('text/javascript');
        return $ret;
    }
}
