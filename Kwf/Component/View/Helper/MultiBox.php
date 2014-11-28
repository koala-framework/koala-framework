<?php
class Kwf_Component_View_Helper_MultiBox
{
    protected $_view;

    public function setView(Kwf_View $view)
    {
        $this->_view = $view;
    }

    public function multiBox($boxName)
    {
        $ret = '';
        foreach ($this->_view->multiBoxes[$boxName] as $c) {
            $ret .= "<div class=\"box\">\n";
            $ret .= $this->_view->component($c)."\n";
            $ret .= "</div>\n";
        }
        return $ret;
    }
}
