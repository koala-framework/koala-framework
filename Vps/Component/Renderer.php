<?php
class Vps_Component_Renderer extends Vps_Component_Renderer_Abstract
{
    private $_renderMaster;
    public function renderMaster($component)
    {
        $this->_renderMaster = true;
        return parent::renderComponent($component);
    }

    public function renderComponent($component)
    {
        $this->_renderMaster = false;
        return parent::renderComponent($component);
    }

    protected function _renderComponentContent($component)
    {
        if ($this->_renderMaster) {
            if ($this->_enableCache && isset($this->_cache['page'][$component->componentId][''])) {
                $content = $this->_cache['page'][$component->componentId][''];
            } else {
                $masterHelper = new Vps_Component_View_Helper_Master();
                $masterHelper->setRenderer($this);
                $content = $masterHelper->master($component);
                if ($this->_enableCache) {
                    Vps_Component_Cache::getInstance()
                        ->save($component, $content, 'page', '', true);
                }
            }
            return $content;
        } else {
            return parent::_renderComponentContent($component);
        }
    }
}
