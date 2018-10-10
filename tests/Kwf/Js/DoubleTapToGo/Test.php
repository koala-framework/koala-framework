<?php
class Kwf_Js_DoubleTapToGo_Test extends PHPUnit_Framework_TestCase
{
    public function testIt()
    {
        $mimeTypes = array('text/javascript', 'text/css');
        $p = new Kwf_Assets_Package_TestPackage('Kwf_Js_DoubleTapToGo');
        foreach ($mimeTypes as $mimeType) {
            foreach ($p->getFilteredUniqueDependencies($mimeType) as $dep) {
                $dep->warmupCaches();
            }
        }

        $cmd = "phantomjs --web-security=false ../vendor/bower_components/qunit-phantomjs-runner/runner.js ";
        $cmd .= "http://" . Kwf_Config::getValue('server.domain') . "/kwf/test/kwf_js_double-tap-to-go_test 20";
        $cmd .= " 2>&1";
        $out = array();
        exec($cmd, $out, $retVar);
        $out = implode("\n", $out);
        if ($retVar) {
            $this->fail("qunit test failed: ".$out);
        }
    }
}
