<?php
abstract class Vps_Component_Output_Abstract
{
    private $_ignoreVisible = false;
    private $_viewCache = array();
    protected $_viewClass = 'Vps_View_Component';

    public function renderMaster($component, array $plugins = array())
    {
        return $this->render($component, true, $plugins);
    }

    public function render($component, $masterTemplate = false, array $plugins = array())
    {
        return $this->_render($component->componentId, $component->componentClass, $masterTemplate, $plugins);
    }

    public function setIgnoreVisible($ignoreVisible)
    {
        $this->_ignoreVisible = $ignoreVisible;
    }

    public function ignoreVisible()
    {
        return $this->_ignoreVisible;
    }

    protected function _getComponent($componentId)
    {
        $select = array();
        if ($this->ignoreVisible()) $select['ignoreVisible'] = true;
        $ret = Vps_Component_Data_Root::getInstance()
            ->getComponentById($componentId, $select);
        if (!$ret) throw new Vps_Exception("Can't find component '$componentId' for rendering");
        return $ret;
    }

    protected function _renderView($template, $templateVars)
    {
        $viewClass = $this->_viewClass;
        $view = new $viewClass();
        $view->assign($templateVars);
        return $view->render($template);
    }
}
