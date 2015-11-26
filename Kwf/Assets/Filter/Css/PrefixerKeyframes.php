<?php
class Kwf_Assets_Filter_Css_PrefixerKeyframes extends Kwf_Assets_Filter_Abstract
{
    protected $_prefix;

    public function __construct($prefix = null)
    {
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
            $cmd = getcwd()."/".VENDOR_PATH."/bin/node ".__DIR__."/PrefixerKeyframes.js ".$this->_prefix." 2>&1";
            $process = new Symfony\Component\Process\Process($cmd);
            $process->setInput($sourcemap->getFileContentsInlineMap(false));

            $process->mustRun();

            $out = $process->getOutput();
            if (Kwf_SourceMaps_SourceMap::hasInline($out)) {
                $ret = Kwf_SourceMaps_SourceMap::createFromInline($out);
            } else {
                $ret = Kwf_SourceMaps_SourceMap::createEmptyMap($out);
                $ret->setMimeType('text/css');
            }
            return $ret;
        } else {
            return $sourcemap;
        }
    }
}
