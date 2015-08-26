<?php
class Kwf_Assets_TinyMce_BuildDependency extends Kwf_Assets_Dependency_Abstract
{
    private $_contentsCache;
    private $_contentsCachePacked;
    private $_contentsCacheSourceMap;

    public function __construct()
    {
    }

    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function warmupCaches()
    {
        if ($this->_contentsCache) return;

        $mtime = null;
        $it = new RecursiveDirectoryIterator(getcwd() . '/' . VENDOR_PATH . '/bower_components/tinymce/js/tinymce');
        $it = new RecursiveIteratorIterator($it);
        foreach ($it as $i) {
            $mtime = max($mtime, $i->getMTime());
        }

        if (!file_exists('temp/tinymce-build-out.js.mtime') || file_get_contents('temp/tinymce-build-out.js.mtime') != $mtime) {
            $cmd = getcwd()."/".VENDOR_PATH."/bin/node ".__DIR__."/build.js";
            putenv("NODE_PATH=".KWF_PATH."/node_modules");
            exec($cmd, $out, $ret);
            putenv("NODE_PATH=");
            if ($ret) {
                throw new Kwf_Exception("tinymce build failed: ".implode("\n", $out));
            }
            if (!file_exists('temp/tinymce-build-out.js')) {
                throw new Kwf_Exception("TinyMce build not found");
            }
            file_put_contents('temp/tinymce-build-out.js.mtime', $mtime);
        }



        $buildFile = sys_get_temp_dir().'/kwf-uglifyjs/tinymce/'.md5(file_get_contents('temp/tinymce-build-out.js'));

        if (!file_exists("$buildFile.min.js")) {
            $dir = dirname($buildFile);
            if (!file_exists($dir)) mkdir($dir, 0777, true);
            copy('temp/tinymce-build-out.js', $buildFile);
            Kwf_Assets_Dependency_Filter_UglifyJs::build($buildFile, 'temp/tinymce-build-out.js');
        }
        $this->_contentsCache = file_get_contents("$buildFile");
        $this->_contentsCachePacked = file_get_contents("$buildFile.min.js");
        $this->_contentsCacheSourceMap = file_get_contents("$buildFile.min.js.map.json");
    }

    public function getContents($language)
    {
        $this->warmupCaches();
        return $this->_contentsCache;
    }

    public function getContentsPacked($language)
    {
        $this->warmupCaches();
        return new Kwf_SourceMaps_SourceMap($this->_contentsCacheSourceMap, $this->_contentsCachePacked);
    }

    public function __toString()
    {
        return 'tinymce';
    }
}
