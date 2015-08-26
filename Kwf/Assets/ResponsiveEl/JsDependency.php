<?php
class Kwf_Assets_ResponsiveEl_JsDependency extends Kwf_Assets_Dependency_Abstract
{
    private $_selector;
    private $_breakpoints;
    private $_dependencyName;

    public function __construct($selector, $breakpoints, $dependencyName)
    {
        $this->_selector = $selector;
        $this->_breakpoints = $breakpoints;
        $this->_dependencyName = $dependencyName;
        $this->setIsCommonJsEntry(true);
    }

    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContents($language)
    {
        return "require('kwf/responsive-el')('".$this->_selector."', [".implode(',', $this->_breakpoints)."]);\n";
    }
    public function __toString()
    {
        return $this->_dependencyName;
    }

}
