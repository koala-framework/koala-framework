<?php
class Kwf_Assets_Modernizr_Dependency extends Kwf_Assets_Dependency_Abstract
{
    private $_features = array();
    private $_fileNameCache;

    public function addFeature($feature)
    {
        $this->_features[] = $feature;
        unset($this->_fileNameCache);
    }

    public function getFeatures()
    {
        return $this->_features;
    }

    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContents($language)
    {
        return file_get_contents($this->getAbsoluteFileName());
    }

    public function getAbsoluteFileName()
    {
        if (isset($this->_fileNameCache)) return $this->_fileNameCache;

        if (!$this->_features) return null;

        $requiredFeatures = $this->_features;
        sort($requiredFeatures);

        $path = Kwf_Config::getValue('path.modernizr');
        $builds = json_decode(file_get_contents($path.'/builds.json'), true);
        foreach ($builds as $build) {
            $features = $build['features'];
            sort($features);
            if ($requiredFeatures == $features) {
                $this->_fileNameCache = $path.'/'.$build['file'];
                return $this->_fileNameCache;
            }
        }
        throw new Kwf_Exception("Can't find generated Modernizr file with following ".count($this->_features)." features: ".implode(', ', $this->_features));
    }

    public function getMTime()
    {
        return filemtime($this->getAbsoluteFileName());
    }

    public function __toString()
    {
        return 'Modernizr('.implode(',', $this->_features).')';
    }
}
