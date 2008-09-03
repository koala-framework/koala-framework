<?php
class Vpc_MultiBox_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['boxes'] = array();
        $boxname = $this->getData()->box;
        foreach ($this->getData()->getPage()->getChildMultiBoxes() as $box) {
            if ($box->multiBox == $boxname) {
                $ret['boxes'][] = $box;
            }
        }
        usort($ret['boxes'], array("Vpc_MultiBox_Component", "sortBoxes"));
        return $ret;
    }
    
    public static function sortBoxes($a, $b) {
        return $a->priority < $b->priority;
    }
}
