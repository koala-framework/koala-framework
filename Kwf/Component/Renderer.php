<?php
class Kwf_Component_Renderer extends Kwf_Component_Renderer_Abstract
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
            if (!$this->_enableCache ||
                ($content = Kwf_Component_Cache::getInstance()->load($component, 'page')) === null) {
                $masterHelper = new Kwf_Component_View_Helper_Master();
                $masterHelper->setRenderer($this);
                $content = $masterHelper->master($component);
                if ($this->_enableCache) {
                    Kwf_Component_Cache::getInstance()
                        ->save($component, $content, 'page', '', true);
                }
            }
            if ($content == Kwf_Component_Cache::NO_CACHE) {
                //TODO: entfernen wenn nie auftritt
                throw new Kwf_Exception("something is very wrong");
            }
            return $content;
        } else {
            return parent::_renderComponentContent($component);
        }
    }

    protected function _getCacheName()
    {
        return 'component';
    }
}
