<?php
class Vpc_ShowContent_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $vars = parent::getTemplateVars();
        $vars['componentId'] = $this->getTreeCacheRow()->tag;
        return $vars;
    }
}
