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

    private function _getOutputFile()
    {
        if (isset($this->_outputFile)) {
            return $this->_outputFile;
        }
        $modernizrPath = dirname(dirname(dirname(dirname(__FILE__)))).'/node_modules/modernizr';
        $package = json_decode(file_get_contents($modernizrPath.'/package.json'), true);

        $this->_outputFile = getcwd().'/temp/modernizr-'.$package['version'].'-'
            .Kwf_Config::getValue('application.uniquePrefix')
            .'-'.implode('-', $this->_features)
            .'/modernizr.js';
        return $this->_outputFile;
    }

    public function getContentsPacked($language)
    {
        if (isset($this->_contentsCache)) return $this->_contentsCache;

        if (!$this->_features) return null;

        $modernizrPath = dirname(dirname(dirname(dirname(__FILE__)))).'/node_modules/modernizr';
        $outputFile = $this->_getOutputFile();

        if (file_exists($outputFile)) {
            $ret = Kwf_SourceMaps_SourceMap::createEmptyMap(file_get_contents($outputFile));
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
        $cmd = getcwd()."/".VENDOR_PATH."/bin/node $modernizrPath/bin/modernizr --config $configFile --uglify --dest ".dirname($outputFile);
        exec($cmd, $out, $retVar);
        unlink($configFile);
        if ($retVar) {
            throw new Kwf_Exception("modernizr failed: ".implode("\n", $out));
        }
        $ret = file_get_contents($outputFile);

        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap($ret);
        $this->_contentsCache = $ret;
        return $ret;
    }

    public function __toString()
    {
        return 'Modernizr('.implode(',', $this->_features).')';
    }

    public function getIdentifier()
    {
        return 'Modernizr('.implode(',', $this->_features).')';
    }

    public function usesLanguage()
    {
        return false;
    }
}
