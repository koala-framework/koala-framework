<?php
class Kwf_Update_Action_Db_ConvertFieldModel extends Kwf_Update_Action_Abstract
{
    public $table;
    public $fields;

    public function update()
    {
        parent::update();

        if (!$this->silent) echo "convert fields for $this->table\n";
        foreach (Kwf_Registry::get('db')->query("SELECT id, data FROM $this->table")->fetchAll() as $row) {
            if (!$this->silent) echo ".";
            $data = @unserialize($row['data']);
            if (!$data) $data = array();
            $sql = "UPDATE $this->table SET ";
            foreach ($this->fields as $f) {
                if (isset($data[$f])) {
                    $sql .= "$f=".Kwf_Registry::get('db')->quote($data[$f]).", ";
                    unset($data[$f]);
                }
            }
            $sql .= "data=".Kwf_Registry::get('db')->quote(serialize($data));
            $sql .= " WHERE id=$row[id]";
            Kwf_Registry::get('db')->query($sql);
        }
        
    }
}
