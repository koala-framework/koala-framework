<?php
class Kwf_Assets_Filter_Css_Autoprefixer extends Kwf_Assets_Filter_Abstract
{
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
        $cmd = getcwd()."/".VENDOR_PATH."/bin/node ".__DIR__."/Autoprefixer.js 2>&1";
        $process = new Symfony\Component\Process\Process($cmd);
        $process->setInput($sourcemap->getFileContentsInlineMap(false));

        $process->mustRun();

        $out = $process->getOutput();
        if (Kwf_SourceMaps_SourceMap::hasInline($out)) {
            $ret = Kwf_SourceMaps_SourceMap::createFromInline($out);
        } else {
            $ret = Kwf_SourceMaps_SourceMap::createEmptyMap($out);
        }
        return $ret;
    }
}
