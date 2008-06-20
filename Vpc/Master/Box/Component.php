<?php
class Vpc_Master_Box_Component extends Vpc_Master_Abstract
{
    public function getTemplateVars()
    {
        $vars = parent::getTemplateVars();
        $boxes = $this->getData()->getChildComponents(array('treecache' => 'Vpc_Master_Box_TreeCache'));
        foreach ($boxes as $box) {
            $vars['boxes'][$box->id] = $box->componentId;
        }
        return $vars;
    }
}
