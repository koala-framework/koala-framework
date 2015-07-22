<?php
class Kwf_Assets_CommonJs_Dependency extends Kwf_Assets_Dependency_Abstract
{
    private $_defer;
    private $_contents;
    private $_mimeType;
    public function __construct($contents, $mimeType, $defer)
    {
        $this->_contents = $contents;
        $this->_mimeType = $mimeType;
        $this->_defer = $defer;
    }

    public function getMimeType()
    {
        return $this->_mimeType;
    }

    public function getDeferLoad()
    {
        return $this->_defer;
    }

    public function getContents()
    {
        return $this->_contents;
    }

    public function __toString()
    {
        return $this->_contents;
    }
}
