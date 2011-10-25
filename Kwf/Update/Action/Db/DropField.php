<?php
class Kwf_Update_Action_Db_DropField extends Kwf_Update_Action_Db_Abstract
{
    public $field;

    public function checkSettings()
    {
        parent::checkSettings();
        if (!$this->field) {
            throw new Kwf_ClientException("Required parameter: field");
        }
    }

    public function update()
    {
        if (!$this->silent) echo "drop field $this->field from $this->table\n";
        $table = $this->model->getRow($this->table);
        $field = $table->getChildRows('Fields', $this->model->select()
                    ->whereId($this->field))->current();
        if (!$field) {
            throw new Kwf_ClientException("Field $this->field does not exist");
        }
        $field->delete();

        return array();
    }
}
