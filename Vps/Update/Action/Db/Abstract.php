<?php
abstract class Vps_Update_Action_Db_Abstract extends Vps_Update_Action_Abstract
{
    public $model;
    public $table;

    public function checkSettings()
    {
        parent::checkSettings();
        if (!isset($this->model)) {
            $this->model = new Vps_Db_TablesModel;
        }
        if (!$this->table) {
            throw new Vps_ClientException("Required parameter: table");
        }
    }
}
