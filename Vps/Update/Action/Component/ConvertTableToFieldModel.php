<?php
class Vps_Update_Action_Component_ConvertTableToFieldModel extends Vps_Update_Action_Db_Abstract
{
    public $table;
    public function update()
    {
        $table = $this->model->getRow($this->table);
        if (!$table) {
            throw new Vps_ClientException("Table '{$this->table}' not found");
        }
        $field = $table->getChildRows('Fields', $this->model->select()
                    ->whereId('component_id'))->current();
        if (!$field) {
            throw new Vps_ClientException("Field 'component_id' not found");
        }
        if ($field->key != 'PRI') {
            throw new Vps_ClientException("Field 'component_id' is not the primary key");
        }
        $model = new Vps_Component_FieldModel();
        $rows = Vps_Registry::get('db')->fetchAssoc("SELECT * FROM {$this->table}");
        foreach ($rows as $row) {
            if ($model->getRow($row['component_id'])) {
                throw new Vps_ClientException("entry with component_id '{$row['component_id']}' does already exist");
            }
        }
        foreach ($rows as $row) {
            $r = $model->createRow();
            foreach ($row as $k=>$i) {
                $r->$k = $i;
            }
            $r->save();
        }
    }
}
