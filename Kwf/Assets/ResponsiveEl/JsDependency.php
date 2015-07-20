<?php
class Kwf_Assets_ResponsiveEl_JsDependency extends Kwf_Assets_Dependency_Abstract
{
    private $_selector;
    private $_breakpoints;

    public function __construct($selector, $breakpoints)
    {
        $this->_selector = $selector;
        $this->_breakpoints = $breakpoints;
    }

    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContents($language)
    {
        return "Kwf.Util.ResponsiveEl('".$this->_selector."', [".implode(',', $this->_breakpoints)."]);\n";
    }
}
