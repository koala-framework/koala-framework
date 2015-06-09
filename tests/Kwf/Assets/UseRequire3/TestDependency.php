<?php
class Kwf_Assets_UseRequire3_TestDependency extends Kwf_Assets_Dependency_Abstract
{
    private $_contents;
    public function __construct($contents)
    {
        $this->_contents = $contents;
    }

    public function getContents($language)
    {
        return $this->_contents;
    }


    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function toDebug()
    {
        return $this->_contents."\n";
    }
}
