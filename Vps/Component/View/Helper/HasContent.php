<?php
class Vps_Component_View_Helper_HasContent extends Vps_Component_View_Helper_Abstract
{
    public function hasContent(Vps_Component_Data $target)
    {
        $source = $this->_getView()->data;
        // TODO: wenn source andere Komponenten nach hascontent fragt, landet
        // das nicht hier drinnen
        $sourcePageId = $source->getPage() ? $source->getPage()->componentId : null;
        $targetPageId = $target->getPage() ? $target->getPage()->componentId : null;
        if ($sourcePageId != $targetPageId) {
            Vps_Component_Cache::getInstance()->saveMeta(
                $source,
                new Vps_Component_Cache_Meta_Component($target)
            );
        }
        return $target->getComponent()->hasContent();
    }
}
