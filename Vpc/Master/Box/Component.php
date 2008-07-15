<?php
class Vpc_Master_Box_Component extends Vpc_Master_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => array(),
            'inherit' => false,
            'priority' => 0
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $priorities = array();
        $page = $this->getData()->getPage();

        while ($page) { // Aktuelle inkl. aller Überseiten durchlaufen
            // Boxen für jeweilige Seite holen, falls Überseite nur die mit inherit==true
            $constraints = array(
                'inherit' => $page->componentId != $this->getData()->componentId
            );
            $boxes = $page->getChildBoxes($constraints);
            // Boxen ausgeben, Priorität speichern, wenn gleiche Box mit höher Priorität gefunden
            foreach ($boxes as $box) {
                if (!isset($priorities[$box->box]) || $box->priority > $priorities[$box->box]) {
                    $ret['boxes'][$box->box] = $box;
                    $priorities[$box->box] = $box->priority;
                }
            }
            $page = $page->getParentPage();
        }
        return $ret;
    }
}
