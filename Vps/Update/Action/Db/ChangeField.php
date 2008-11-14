<?php
class Vps_Update_Action_Db_ChangeField extends Vps_Update_Action_Db_Abstract
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
    }

    public function update()
    {
        $table = $this->model->getRow($this->table);
        $field = $table->getChildRows('Fields', $this->model->select()
                    ->whereId($this->field))->current();
        if (!$field) {
            throw new Vps_ClientException("Field $this->field does not exist");
        }
        if (isset($this->type)) $field->type = $this->type;
        if (isset($this->null)) $field->null = $this->null;
        if (isset($this->key)) $field->key = $this->key;
        if (isset($this->default)) $field->default = $this->default;
        if (isset($this->extra)) $field->extra = $this->extra;
        $field->save();

        return array();
    }
}
