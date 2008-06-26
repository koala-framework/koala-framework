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
                $id = $box->componentId;
                if (!isset($priorities[$id]) || $box->priority > $priorities[$id]) {
                    $ret['boxes'][$box->box] = $id;
                    $priorities[$box->box] = $box->priority;
                }
            }
            $page = $page->getParentPage();
        }
        return $ret;
    }
}
