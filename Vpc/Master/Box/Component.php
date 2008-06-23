<?php
class Vpc_Master_Box_Component extends Vpc_Master_Abstract
{
    public function getTemplateVars()
    {
        $vars = parent::getTemplateVars();
        $priorities = array();
        $page = $this->getData();
        while ($page) { // Aktuelle inkl. aller Überseiten durchlaufen
            // Boxen für jeweilige Seite holen, falls Überseite nur die mit inherit==true
            $constraints = array(
                'treecache' => 'Vpc_TreeCache_StaticBox',
                'inherit' => $page->componentId != $this->getData()->componentId
            );
            $boxes = $this->getData()->getPage()->getChildBoxes($constraints);
            // Boxen ausgeben, Priorität speichern, wenn gleiche Box mit höher Priorität gefunden
            foreach ($boxes as $box) {
                $id = $box->componentId;
                if (!isset($priorities[$id] ) || $box->priority > $priorities[$id]) {
                    $vars['boxes'][$box->id] = $id;
                    $priorities[$box->id] = $box->priority;
                }
            }
            $page = $page->getParentPage();
        }
        return $vars;
    }
}
