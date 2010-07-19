<?php
abstract class Vps_View_Helper_Abstract
{
    protected $_view;

    public function setView(Vps_View $view)
    {
        $this->_view = $view;
    }

    /**
     * @return Vps_View
     */
    protected function _getView()
    {
        return $this->_view;
    }
}
