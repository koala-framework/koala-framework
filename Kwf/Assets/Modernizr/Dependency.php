<?php
class Kwf_Assets_Modernizr_Dependency extends Kwf_Assets_Dependency_Abstract
{
    private $_features = array();
    private $_contentsCache;

    public function addFeature($feature)
    {
        $this->_features[] = $feature;
        unset($this->_contentsCache);
    }

    public function getFeatures()
    {
        return $this->_features;
    }

    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function warmupCaches()
    {
        $this->getContents('en');
    }

    public function getContents($language)
    {
        if (isset($this->_contentsCache)) return $this->_contentsCache;

        if (!$this->_features) return null;

        $outputFile = getcwd().'/temp/modernizr-'.implode('-', $this->_features);
        if (file_exists("$outputFile.buildtime") && (time() - file_get_contents("$outputFile.buildtime") < 24*60*60)) {
            $ret = file_get_contents($outputFile);
            $this->_contentsCache = $ret;
            return $ret;
        }

        $extensibility = array(
            "addtest"      => false,
            "prefixed"     => false,
            "teststyles"   => false,
            "testprops"    => false,
            "testallprops" => false,
            "hasevents"    => false,
            "prefixes"     => false,
            "domprefixes"  => false
        );
        $tests = array();
        foreach ($this->_features as $f) {
            if (isset($extensibility[strtolower($f)])) {
                $extensibility[strtolower($f)] = true;
            } else {
                //add two versions of the test
                //requried to support core detects (eg. CssAnimations) and non-core detects (css_mediaqueries)

                $tests[] = strtolower($f);

                $filter = new Zend_Filter_Word_CamelCaseToUnderscore();
                $tests[] = strtolower($filter->filter($f));
            }
        }
        if (Kwf_Config::getValue('application.uniquePrefix')) {
            $extensibility["cssclassprefix"] = Kwf_Config::getValue('application.uniquePrefix').'-';
        }
        $config = array(
            'modernizr' => array(
                'dist' => array(
                    'devFile' => false,
                    'outputFile' => $outputFile,
                    'extra' => array(
                        "shiv"       => false,
                        "printshiv"  => false,
                        "load"       => false,
                        "mq"         => true,
                        "cssclasses" => true,
                    ),
                    'extensibility' => $extensibility,
                    'uglify' => true,
                    'tests' => $tests,
                    'parseFiles' => false,
                    'matchCommunityTests' => false,
                    'customTests' => array()
                )
            )
        );

        $gruntfile  = "    module.exports = function(grunt) {\n";
        $gruntfile .= "    grunt.initConfig(";
        $gruntfile .= json_encode($config);
        $gruntfile .= ");\n";
        $gruntfile .= "    grunt.loadNpmTasks(\"grunt-modernizr\");\n";
        $gruntfile .= "    grunt.registerTask('default', ['modernizr']);\n";
        $gruntfile .= "};\n";

        $cwd = getcwd();
        chdir(dirname(dirname(dirname(dirname(__FILE__)))));
        file_put_contents('Gruntfile.js', $gruntfile);
        $cmd = $cwd."/".VENDOR_PATH."/bin/node ./node_modules/grunt-cli/bin/grunt 2>&1";
        exec($cmd, $out, $retVar);
        unlink('Gruntfile.js');
        if (file_exists($outputFile)) $ret = file_get_contents($outputFile);

        chdir($cwd);
        if ($retVar) {
            throw new Kwf_Exception("Grunt failed: ".implode("\n", $out));
        }
        file_put_contents("$outputFile.buildtime", time());

        $this->_contentsCache = $ret;
        return $ret;
    }

    public function getMTime()
    {
        $outputFile = getcwd().'/temp/modernizr-'.implode('-', $this->_features);
        if (!file_exists("$outputFile.buildtime")) $this->getContents(null);
        return (int)file_get_contents("$outputFile.buildtime");
    }

    public function __toString()
    {
        return 'Modernizr('.implode(',', $this->_features).')';
    }

    public function usesLanguage()
    {
        return false;
    }
}
