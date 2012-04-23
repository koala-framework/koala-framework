<?php
abstract class Kwf_Update_Action_Db_Abstract extends Kwf_Update_Action_Abstract
{
    public $model;
    public $table;

    public function checkSettings()
    {
        parent::checkSettings();
        if (!$this->table) {
            throw new Kwf_ClientException("Required parameter: table");
        }
    }

    public function update()
    {
        if (!isset($this->model)) {
            $this->model = new Kwf_Db_TablesModel;
        }
    }
}
