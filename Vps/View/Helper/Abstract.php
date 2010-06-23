<?php
abstract class Vps_View_Helper_Abstract
{
    protected $_view;

    public function setView($view)
    {
        $this->_view = $view;
    }

    protected function _getView()
    {
        return $this->_view;
    }
}
