<?php
class Vps_Update_Action_Db_AddField extends Vps_Update_Action_Db_Abstract
{
    public $field;
    public $type;
    public $null;
    public $key;
    public $default;
    public $extra;

    public function checkSettings()
    {
        parent::checkSettings();
        if (!$this->field) {
            throw new Vps_ClientException("Required parameter: field");
        }
        if (!$this->type) {
            throw new Vps_ClientException("Required parameter: type");
        }
    }

    public function update()
    {
        if (!$this->silent) echo "add field $this->field to $this->table\n";
        $table = $this->model->getRow($this->table);
        if (!$table) {
            throw new Vps_ClientException("Table '$this->table' not found");
        }
        $field = $table->getChildRows('Fields', $this->model->select()
                    ->whereId($this->field))->current();
        if ($field) {
            throw new Vps_ClientException("Field $this->field does alredy exist");
        }
        $field = $table->createChildRow('Fields');
        $field->field = $this->field;
        $field->type = $this->type;
        if (isset($this->null)) $field->null = $this->null;
        if (isset($this->key)) $field->key = $this->key;
        if (isset($this->default)) $field->default = $this->default;
        if (isset($this->extra)) $field->extra = $this->extra;
        $field->save();


        return array();
    }
}
