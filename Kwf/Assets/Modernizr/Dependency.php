<?php
class Kwf_Assets_Modernizr_Dependency extends Kwf_Assets_Dependency_Abstract
{
    private $_features = array();
    private $_contentsCache;
    private $_outputFile;

    public function addFeature($feature)
    {
        $this->_features[] = $feature;
        unset($this->_contentsCache);
        unset($this->_outputFile);
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

        $modernizrPath = dirname(dirname(dirname(dirname(__FILE__)))).'/node_modules/modernizr';
        $package = json_decode(file_get_contents($modernizrPath.'/package.json'), true);

        $this->_outputFile = getcwd().'/temp/modernizr-'.$package['version'].'-'.implode('-', $this->_features).'/modernizr.js';

        if (file_exists($this->_outputFile)) {
            $ret = file_get_contents($this->_outputFile);
            $this->_contentsCache = $ret;
            return $ret;
        }

        $configAll = json_decode(file_get_contents($modernizrPath.'/lib/config-all.json'), true);

        $allFeatureDetects = $configAll['feature-detects'];

        $options = array();
        foreach ($configAll['options'] as $i) {
            $options[$i] = false;
        }
        $options['mq'] = true;
        $options['setClasses'] = true;

        $featureDetects = array();
        foreach ($this->_features as $f) {
            if (isset($options[strtolower($f)])) {
                //for prefixed
                $options[strtolower($f)] = true;
            } else {
                $filter = new Zend_Filter_Word_CamelCaseToSeparator('/');
                $fFiltered = strtolower($filter->filter($f));
                if (!in_array($fFiltered, $allFeatureDetects)) {
                    throw new Kwf_Exception("Invalid Modernizr Dependency, test doesn't exist: '".$f."'");
                }
                $featureDetects[] = $fFiltered;
            }
        }

        foreach ($options as $k=>$i) {
            if (!$i) unset($options[$k]);
        }
        $options = array_keys($options);

        $classPrefix = '';
        if (Kwf_Config::getValue('application.uniquePrefix')) {
            $classPrefix = Kwf_Config::getValue('application.uniquePrefix').'-';
        }

        $config = array(
            'classPrefix' => $classPrefix,
            'options' => $options,
            'feature-detects' => $featureDetects
        );
        $configFile = tempnam('temp/', 'modernizrbuild');
        unlink($configFile);
        $configFile .= '.json';
        file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
        $cmd = "./".VENDOR_PATH."/bin/node $modernizrPath/bin/modernizr --config $configFile --uglify --dest ".dirname($this->_outputFile);
        exec($cmd, $out, $retVar);
        unlink($configFile);
        if ($retVar) {
            throw new Kwf_Exception("modernizr failed: ".implode("\n", $out));
        }
        $ret = file_get_contents($this->_outputFile);

        $this->_contentsCache = $ret;
        return $ret;
    }

    public function getMTime()
    {
        if (!isset($this->_outputFile)) $this->getContents(null);
        return filemtime($this->_outputFile);
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
