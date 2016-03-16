<?php
class Kwf_Component_PluginRoot_MaskComponentLink_Plugin extends Kwf_Component_PluginRoot_ComponentLinkDynamicCutter
{
    protected function _getMaskType(Kwf_Component_Data $page)
    {
        if ($page->componentId == 2) {
            return self::MASK_TYPE_HIDE;
        } else if ($page->componentId == 3) {
            return self::MASK_TYPE_SHOW;
        }
        return parent::_getMaskType($page);
    }

    protected function _getMaskParams(Kwf_Component_Data $page)
    {
        if ($page->componentId == 2) {
            return array('foo' => 'a');
        } else if ($page->componentId == 3) {
            return array('foo' => 'b');
        }
        return parent::_getMaskParams($page);
    }

    // Make method public for testing

    public function removeMaskedComponentLinks($output, $params = null)
    {
        return $this->_removeMaskedComponentLinks($output, $params);
    }

    public function removeMasksFromComponentLinks($output, $params = null)
    {
        return $this->_removeMasksFromComponentLinks($output, $params);
    }

    public function getMaskedContentParts($output, $maskType = null, $params = null)
    {
        return $this->_getMaskedContentParts($output, $maskType, $params);
    }
}
