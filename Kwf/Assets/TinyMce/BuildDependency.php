<?php
class Kwf_Assets_TinyMce_BuildDependency extends Kwf_Assets_Dependency_File_Js
{
    public function __construct()
    {
        parent::__construct('temp/tinymce-build-out.js');
    }

    public function warmupCaches()
    {
        $cmd = "node ".__DIR__."/build.js";
        putenv("NODE_PATH=".KWF_PATH."/node_modules");
        exec($cmd, $out, $ret);
        putenv("NODE_PATH=");
        if ($ret) {
            throw new Kwf_Exception("tinymce build failed: ".implode("\n", $out));
        }
        if (!file_exists('temp/tinymce-build-out.js')) {
            throw new Kwf_Exception("TinyMce build not found");
        }
        parent::warmupCaches();
    }
}
