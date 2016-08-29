<?php
class Kwf_Assets_Dependency_Filter_BabelJs
{
    private $_sourceFile;

    public function __construct($sourceFile)
    {
        $this->_sourceFile = $sourceFile;
    }

    public function build()
    {
        $babel = getcwd()."/".VENDOR_PATH."/bin/node ".getcwd().'/node_modules/babel-cli/bin/babel.js';
        $cmd = "$babel ";
        $arguments = implode(' ', array_values($this->_getArguments()));
        $cmd .= "{$arguments} ";
        $cmd .= escapeshellarg($this->_sourceFile);
        $cmd .= " 2>&1";
        $out = array();
        $nodePath = implode(PATH_SEPARATOR, $this->_getNodePath());
        putenv("NODE_PATH={$nodePath}");
        exec($cmd, $out, $retVal);
        putenv("NODE_PATH=");

        if ($retVal) {
            throw new Kwf_Exception("babel failed: ".implode("\n", $out));
        }
        $contents = implode("\n", $out);
        return Kwf_SourceMaps_SourceMap::createFromInline($contents);
    }

    protected function _getArguments()
    {
        return array(
            'minified' => '--minified',
            'noBabelrc' => '--no-babelrc',
            'sourceMaps' => '--source-maps inline',
            'presets' => '--presets es2015',
            'plugins' => '--plugins transform-runtime'
        );
    }

    protected function _getNodePath()
    {
        return array(
            getcwd()."/node_modules",
            getcwd()."/"
        );
    }
}
