<?php
class Kwf_Component_RootPlugin_MaskComponentLink_Plugin extends Kwf_Component_Data_RootPlugin_MaskComponentLink
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
}
