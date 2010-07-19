<?php
class Vps_Component_Renderer extends Vps_Component_Renderer_Abstract
{
    private $_isRenderMaster = false;

    public function renderMaster($component)
    {
        return $this->renderComponent($component, true);
    }

    public function renderComponent($component, $renderMaster = false)
    {
        $this->_isRenderMaster = $renderMaster;
        return parent::renderComponent($component);
    }

    protected function _getView()
    {
        $ret = parent::_getView();
        $ret->setIsRenderMaster($this->_isRenderMaster);
        return $ret;
    }
}
