<?php
class Kwf_Update_Action_Component_ConvertToChainedTrl extends Kwf_Update_Action_Abstract
{
    public $rootPrefix = 'root-';
    public $newRootPrefix = 'root-master-';
    public function update()
    {
        $db = Zend_Registry::get('db');
        $db->query("UPDATE kwf_pages
            SET parent_id=CONCAT('".$this->newRootPrefix."', MID(parent_id, ".(strlen($this->rootPrefix)+1)."))
            WHERE parent_id LIKE ".$db->quote(str_replace('_', '\_', $this->rootPrefix).'%'));

        $db->query("UPDATE kwf_pages
            SET parent_subroot_id=".$db->quote(substr($this->newRootPrefix, 0, -1))."
            WHERE parent_subroot_id = ".$db->quote(substr($this->rootPrefix, 0, -1)));

        foreach ($db->query("SHOW TABLES")->fetchAll() as $table) {
            $table = array_values($table);
            $table = $table[0];
            $hasComponentId = false;
            foreach ($db->query("SHOW FIELDS FROM $table")->fetchAll() as $field) {
                if ($field['Field'] == 'component_id') $hasComponentId = true;
            }
            if ($hasComponentId) {
                $db->query("UPDATE $table SET component_id =
                        CONCAT('".$this->newRootPrefix."', MID(component_id, ".(strlen($this->rootPrefix)+1)."))
                        WHERE component_id LIKE ".$db->quote(str_replace('_', '\_', $this->rootPrefix).'%'));
            }
        }
        $db->query("UPDATE kwc_basic_text SET content =
                REPLACE(content, 'href=\"$this->rootPrefix', 'href=\"".$this->newRootPrefix."')");
        //TODO: Images

    }
}
