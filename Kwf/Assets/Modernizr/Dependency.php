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
                $filter = new Zend_Filter_Word_CamelCaseToSeparator('/');
                $featureString = strtolower($filter->filter($f));
                //!!TODO update dependencies in components to match correct string
                if ($featureString == 'boxshadow') {
                    $featureString = 'css/boxshadow';
                } else if ($featureString == 'touch') {
                    $featureString = 'touchevents';
                } else if ($f == 'CssTransforms3D') {
                    $featureString = 'css/transforms3d';
                }
                $tests[] = $featureString;
            }
        }

        $options = array();
        foreach ($extensibility as $key => $value) {
            if ($value) $options[] = $key;
        }
        $newConfig = array(
            'minify' => true,
            'classPrefix' => '',
            'options' => $options,
            'feature-detects' => $tests
        );

        if (file_exists($outputFile)) unlink($outputFile);

        $cwd = getcwd();
        chdir(dirname(dirname(dirname(dirname(__FILE__)))));
        file_put_contents('modernizr.config.json', json_encode($newConfig));
        $cmd = $cwd."/".VENDOR_PATH."/bin/node ./node_modules/modernizr/bin/modernizr -c modernizr.config.json";
        exec($cmd, $out, $retVar);

        rename('modernizr.js', $outputFile); // command does only accept folder where to put modernizr.js
        unlink('modernizr.config.json');
        if (file_exists($outputFile)) $ret = file_get_contents($outputFile);
        chdir($cwd);
        if ($retVar) {
            throw new Kwf_Exception("Modernizr failed: ".implode("\n", $out));
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
}
