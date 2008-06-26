<?php
class Vpc_Master_Box_Component extends Vpc_Master_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $priorities = array();
        $page = $this->getData()->getPage();

        while ($page) { // Aktuelle inkl. aller Überseiten durchlaufen
            // Boxen für jeweilige Seite holen, falls Überseite nur die mit inherit==true
            $constraints = array(
                'treecache' => 'Vpc_TreeCache_StaticBox',
                'inherit' => $page->componentId != $this->getData()->componentId
            );
            $boxes = $page->getChildBoxes($constraints);
            // Boxen ausgeben, Priorität speichern, wenn gleiche Box mit höher Priorität gefunden
            foreach ($boxes as $box) {
                if (!isset($priorities[$box->box]) || $box->priority > $priorities[$box->box]) {
                    $ret['boxes'][$box->box] = $box->componentId;
                    $priorities[$box->box] = $box->priority;
                }
            }
            $page = $page->getParentPage();
        }
        return $ret;
    }
}
