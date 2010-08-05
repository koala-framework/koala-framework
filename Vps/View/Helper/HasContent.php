<?php
class Vps_View_Helper_HasContent extends Vps_View_Helper_Abstract
{
    public function hasContent(Vps_Component_Data $component)
    {
        $target = $this->_getView()->data;
        $sourcePageId = $component->getPage() ? $component->getPage()->componentId : null;
        $targetPageId = $target->getPage() ? $target->getPage()->componentId : null;
        if ($sourcePageId != $targetPageId) {
            Vps_Component_Cache::getInstance()->saveMetaComponent($component, $target);
        }
        return $component->getComponent()->hasContent();
    }
}
