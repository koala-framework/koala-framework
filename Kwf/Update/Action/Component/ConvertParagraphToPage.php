<?php
class Kwf_Update_Action_Component_ConvertParagraphToPage extends Kwf_Update_Action_Abstract
{
    public function checkSettings()
    {
        parent::checkSettings();
        if (!$this->key || !$this->class) {
            throw new Kwf_Exception_Client("Required parameters: key, class");
        }
    }

    public function update()
    {
        $paragraphs = Kwf_Model_Abstract::getInstance('Kwc_Paragraphs_Model');
        $pages = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_GeneratorModel');
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

            $update = new Kwf_Update_Action_Component_ConvertComponentIds(array(
                'search' => $row->component_id . '-' . $row->id,
                'replace' => $row->component_id,
                'pattern' => $row->component_id . '-' . $row->id . '%'
            ));
            $update->update();

            $row->delete();
        }
    }
}
