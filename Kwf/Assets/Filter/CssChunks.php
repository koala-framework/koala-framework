<?php
class Kwf_Assets_Filter_CssChunks
{
    public function filter(Kwf_SourceMaps_SourceMap $sourcemap)
    {
        $cmd = getcwd()."/".VENDOR_PATH."/bin/node ".__DIR__."/CssChunks.js";
        $cmd .= " 2>&1";
        $process = new Symfony\Component\Process\Process($cmd);
        $process->setEnv(array(
            'NODE_PATH' => getcwd().'/'.KWF_PATH.'/node_modules'
        ));
        $process->setInput($sourcemap->getFileContentsInlineMap(false));

        $process->mustRun();

        $out = $process->getOutput();
        $out = explode("\n/* ***** NEXT CHUNK ***** */\n", $out);
        $ret = array();
        foreach ($out as $chunk) {
            if (Kwf_SourceMaps_SourceMap::hasInline($chunk)) {
                $mapChunk = Kwf_SourceMaps_SourceMap::createFromInline($chunk);
            } else {
                $mapChunk = Kwf_SourceMaps_SourceMap::createEmptyMap($chunk);
                $mapChunk->setMimeType('text/css');
            }
            $ret[] = $mapChunk;
        }
        return $ret;
    }
}
