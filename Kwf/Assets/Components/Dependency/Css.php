<?php
class Kwf_Assets_Components_Dependency_Css extends Kwf_Assets_Components_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/css';
    }

    public function getContentsPacked()
    {
        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap('');
        $masterFiles = array();
        foreach ($this->_componentDependencies as $dep) {
            $c = $dep->getContentsPacked();
            $data = $c->getMapContentsData(false);
            if (isset($data->{'_x_org_koala-framework_masterFiles'})) {
                $masterFiles = array_merge($masterFiles, $data->{'_x_org_koala-framework_masterFiles'});
            }

            $sourcesCount = 0;
            $packageData = $ret->getMapContentsData(false);
            if (isset($packageData->sources)) {
                $sourcesCount = count($packageData->sources);
            }
            unset($packageData);

            $replacements = array();
            if (Kwf_Config::getValue('application.uniquePrefix')) {
                $replacements['kwcBem--'] = $this->_getKwcClass().'--';
                $replacements['kwcBem__'] = $this->_getKwcClass().'__';
            } else {
                $replacements['kwcBem--'] = '';
                $replacements['kwcBem__'] = '';
            }
            $replacements['kwcClass'] = $this->_getKwcClass();
            $filter = new Kwf_Assets_Filter_Css_SelectorReplace($replacements);
            $c = $filter->filter($c);

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
        $ret->{'_x_org_koala-framework_masterFiles'} = $masterFiles;




        $ret->setMimeType('text/css');
        return $ret;
    }
}
