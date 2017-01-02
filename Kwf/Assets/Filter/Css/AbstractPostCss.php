<?php
abstract class Kwf_Assets_Filter_Css_AbstractPostCss extends Kwf_Assets_Filter_Abstract
{
    public function getMimeType()
    {
        return 'text/css';
    }

    abstract public function getPluginName();
    abstract public function getMasterFiles();

    public function getPluginOptions(Kwf_Assets_Dependency_Abstract $dependency = null)
    {
        return array();
    }

    public function filter(Kwf_SourceMaps_SourceMap $sourcemap, Kwf_Assets_Dependency_Abstract $dependency = null)
    {
        $pluginsInitCode = "plugins.push(require('".$this->getPluginName()."')(".json_encode((object)$this->getPluginOptions($dependency))."));";
        $ret = Kwf_Assets_Filter_Css_PostCssRunner::run($pluginsInitCode, $sourcemap);

        $data = $ret->getMapContentsData(false);
        $inData = $sourcemap->getMapContentsData(false);

        if (isset($inData->{'_x_org_koala-framework_masterFiles'})) {
            $data->{'_x_org_koala-framework_masterFiles'} = $inData->{'_x_org_koala-framework_masterFiles'};
        } else {
            $data->{'_x_org_koala-framework_masterFiles'} = array();
        }
        foreach ($this->getMasterFiles() as $file) {
            if (!in_array($file, $data->{'_x_org_koala-framework_masterFiles'})) {
                $data->{'_x_org_koala-framework_masterFiles'}[] = $file;
            }
        }

        if (isset($inData->{'_x_org_koala-framework_sourcesContent'})) {
            $data->{'_x_org_koala-framework_sourcesContent'} = $inData->{'_x_org_koala-framework_sourcesContent'};
        }

        return $ret;
    }
}
