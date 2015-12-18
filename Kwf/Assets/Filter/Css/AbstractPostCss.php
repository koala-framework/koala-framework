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

        $sources = $ret->getSources();
        foreach ($this->getMasterFiles() as $file) {
            if (!in_array($file, $sources)) $ret->addSource($file);
        }

        return $ret;
    }
}
