<?php
class Kwf_Assets_Filter_Css_PrefixerFontface extends Kwf_Assets_Filter_Abstract
{
    protected $_prefix;

    public function __construct($prefix = NULL) {
        $this->_prefix = $prefix ? $prefix : Kwf_Config::getValue('application.uniquePrefix');
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
        if ($this->_prefix) {
            $cmd = getcwd()."/".VENDOR_PATH."/bin/node ".__DIR__."/PrefixerFontface.js ".$this->_prefix." 2>&1";
            $process = new Symfony\Component\Process\Process($cmd);
            $process->setInput($sourcemap->getFileContentsInlineMap(false));

            $process->mustRun();

            $out = $process->getOutput();
            return Kwf_SourceMaps_SourceMap::createFromInline($out);
        }
    }
}
