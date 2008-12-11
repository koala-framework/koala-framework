<?php
abstract class Vps_Component_Output_Abstract
{
    private $_ignoreVisible = false;
    private $_viewCache = array();

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
        $ret = Vps_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => $this->ignoreVisible()));
        if (!$ret) throw new Vps_Exception("Can't find component '$componentId' for rendering");
        return $ret;
    }

    protected function _renderView($template, $templateVars)
    {
        $view = new Vps_View_Component();
        $view->assign($templateVars);
        return $view->render($template);
    }

    protected function _hasViewCache($componentClass)
    {
        if (!isset($this->_viewCache[$componentClass])) {
            $this->_viewCache[$componentClass] = Vpc_Abstract::getSetting($componentClass, 'viewCache');
        }
        return $this->_viewCache[$componentClass];
    }

}
