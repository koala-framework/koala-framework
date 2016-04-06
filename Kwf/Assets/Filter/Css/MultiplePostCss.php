<?php
class Kwf_Assets_Filter_Css_MultiplePostCss extends Kwf_Assets_Filter_Abstract
{
    protected $_filters = array();

    public function __construct(array $filters)
    {
        $this->_filters = $filters;
    }

    public function getExecuteFor()
    {
        return self::EXECUTE_FOR_DEPENDENCY;
    }

    public function getMimeType()
    {
        return 'text/css';
    }

    public function filter(Kwf_SourceMaps_SourceMap $sourcemap)
    {
        $pluginsInitCode = "";
        foreach ($this->_filters as $f) {
            $pluginsInitCode .= "plugins.push(require('".$f->getPluginName()."')(".json_encode((object)$f->getPluginOptions())."));\n";
        }
        $ret = Kwf_Assets_Filter_Css_PostCssRunner::run($pluginsInitCode, $sourcemap);

        $data = $ret->getMapContentsData(false);
        if (!isset($data->{'_x_org_koala-framework_masterFiles'})) {
            $data->{'_x_org_koala-framework_masterFiles'} = array();
        }
        foreach ($this->_filters as $f) {
            foreach ($f->getMasterFiles() as $file) {
                if (!in_array($file, $data->{'_x_org_koala-framework_masterFiles'})) {
                    $data->{'_x_org_koala-framework_masterFiles'}[] = $file;
                }
            }
        }


        $inData = $sourcemap->getMapContentsData(false);
        if (isset($inData->{'_x_org_koala-framework_sourcesContent'})) {
            $data->{'_x_org_koala-framework_sourcesContent'} = $inData->{'_x_org_koala-framework_sourcesContent'};
        }

        return $ret;
    }
}
