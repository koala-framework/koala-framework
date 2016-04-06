<?php
abstract class Kwf_Assets_Filter_Css_AbstractPostCss extends Kwf_Assets_Filter_Abstract
{
    public function getMimeType()
    {
        return 'text/css';
    }

    abstract public function getPluginName();
    abstract public function getMasterFiles();

    public function getPluginOptions()
    {
        return array();
    }

    public function filter(Kwf_SourceMaps_SourceMap $sourcemap)
    {
        $pluginsInitCode = "plugins.push(require('".$this->getPluginName()."')(".json_encode((object)$this->getPluginOptions())."));";
        $ret = Kwf_Assets_Filter_Css_PostCssRunner::run($pluginsInitCode, $sourcemap);

        $data = $ret->getMapContentsData(false);
        if (!isset($data->{'_x_org_koala-framework_masterFiles'})) {
            $data->{'_x_org_koala-framework_masterFiles'} = array();
        }
        foreach ($this->getMasterFiles() as $file) {
            if (!in_array($file, $data->{'_x_org_koala-framework_masterFiles'})) {
                $data->{'_x_org_koala-framework_masterFiles'}[] = $file;
            }
        }

        $inData = $sourcemap->getMapContentsData(false);
        if (isset($inData->{'_x_org_koala-framework_sourcesContent'})) {
            $data->{'_x_org_koala-framework_sourcesContent'} = $inData->{'_x_org_koala-framework_sourcesContent'};
        }

        return $ret;
    }
}
