<?php
class Vps_Update_Action_Db_ConvertFieldModel extends Vps_Update_Action_Abstract
{
    public $table;
    public $fields;

    public function update()
    {
        if (!$this->silent) echo "convert fields for $this->table\n";
        foreach (Vps_Registry::get('db')->query("SELECT id, data FROM $this->table")->fetchAll() as $row) {
            if (!$this->silent) echo ".";
            $data = @unserialize($row['data']);
            if (!$data) $data = array();
            $sql = "UPDATE $this->table SET ";
            foreach ($this->fields as $f) {
                if (isset($data[$f])) {
                    $sql .= "$f=".Vps_Registry::get('db')->quote($data[$f]).", ";
                    unset($data[$f]);
                }
            }
            $sql .= "data=".Vps_Registry::get('db')->quote(serialize($data));
            $sql .= " WHERE id=$row[id]";
            Vps_Registry::get('db')->query($sql);
        }
        
    }
}
