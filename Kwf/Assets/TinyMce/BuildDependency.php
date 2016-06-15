<?php
class Kwf_Assets_TinyMce_BuildDependency extends Kwf_Assets_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContentsPacked()
    {
        $mtime = filemtime(__DIR__."/build.js");
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



        $buildFile = sys_get_temp_dir().'/kwf-uglifyjs/tinymce/v2-'.md5(file_get_contents('temp/tinymce-build-out.js'));

        if (!file_exists("$buildFile.min.js")) {
            $dir = dirname($buildFile);
            if (!file_exists($dir)) mkdir($dir, 0777, true);
            copy('temp/tinymce-build-out.js', $buildFile);
            Kwf_Assets_Dependency_Filter_UglifyJs::build($buildFile, '/assets/web/temp/tinymce-build-out.js');
        }

        $ret = new Kwf_SourceMaps_SourceMap(file_get_contents("$buildFile.min.js.map.json"), file_get_contents("$buildFile.min.js"));
        $data = $ret->getMapContentsData(false);
        $data->{'_x_org_koala-framework_sourcesContent'}[0] = file_get_contents('temp/tinymce-build-out.js');
        return $ret;
    }

    public function __toString()
    {
        return 'tinymce';
    }

    public function getIdentifier()
    {
        return 'tinymce';
    }
}
