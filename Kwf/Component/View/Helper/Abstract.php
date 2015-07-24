<?php
abstract class Kwf_Component_View_Helper_Abstract
{
    protected $_view;
    protected $_renderer;

    public function setView($view)
    {
        $this->_view = $view;
    }

    /**
     * @return Kwf_View
     */
    protected function _getView()
    {
        return $this->_view;
    }

    public function setRenderer(Kwf_Component_Renderer_Abstract $renderer)
    {
        $this->_renderer = $renderer;
    }

    /**
     * @return Kwf_Component_Renderer_Abstract
     */
    protected function _getRenderer()
    {
        return $this->_renderer;
    }

}
