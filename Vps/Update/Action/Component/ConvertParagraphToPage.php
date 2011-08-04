<?php
class Vps_Update_Action_Component_ConvertParagraphToPage extends Vps_Update_Action_Abstract
{
    public function checkSettings()
    {
        parent::checkSettings();
        if (!$this->key || !$this->class) {
            throw new Vps_Exception_Client("Required parameters: key, class");
        }
    }

    public function update()
    {
        $paragraphs = Vps_Model_Abstract::getInstance('Vpc_Paragraphs_Model');
        $pages = Vps_Model_Abstract::getInstance('Vpc_Root_Category_GeneratorModel');
        $select = $paragraphs->select()->whereEquals('component', $this->key);
        foreach ($paragraphs->getRows($select) as $row) {
            if ((int)$row->component_id != $row->component_id) {
                echo $row->id . ' kann nicht konvertiert werden, weil es keine direkte Unterkomponente einer Page ist.' . "\n";;
                continue;
            }
            $page = $pages->getRow($row->component_id);
            if (!$page) {
                echo 'Page nicht gefunden: ' . $row->component_id . "\n";
                continue;
            }
            $page->component = $this->key;
            $page->save();

            $update = new Vps_Update_Action_Component_ConvertComponentIds(array(
                'search' => $row->component_id . '-' . $row->id,
                'replace' => $row->component_id,
                'pattern' => $row->component_id . '-' . $row->id . '%'
            ));
            $update->update();

            $row->delete();
        }
    }
}
