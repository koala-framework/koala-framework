<?php
class Vps_Update_Action_Db_ChangeField extends Vps_Update_Action_Db_Abstract
{
    public $field;
    public $type;
    public $null;
    public $key;
    public $default = false; //php sux (kann sonst nicht auf null gesetzt werden)
    public $extra;

    public function checkSettings()
    {
        parent::checkSettings();
        if (!$this->field) {
            throw new Vps_ClientException("Required parameter: field");
        }
    }

    public function update()
    {
        if (!$this->silent) echo "change field $this->field in $this->table\n";
        $table = $this->model->getRow($this->table);
        if (!$table) {
            throw new Vps_ClientException("Table '$this->table' not found");
        }
        $field = $table->getChildRows('Fields', $this->model->select()
                    ->whereId($this->field))->current();
        if (!$field) {
            throw new Vps_ClientException("Field $this->field does not exist");
        }
        if (isset($this->type)) $field->type = $this->type;
        if (isset($this->null)) $field->null = $this->null;
        if (isset($this->key)) $field->key = $this->key;
        if ($this->default !== false) $field->default = $this->default;
        if (isset($this->extra)) $field->extra = $this->extra;
        $field->save();

        return array();
    }
}
