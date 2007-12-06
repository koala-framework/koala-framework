<?php
class Vps_Auto_Grid_Column_Button extends Vps_Auto_Grid_Column
{
    public function __construct($dataIndex = null, $header = '', $width = 50)
    {
        parent::__construct($dataIndex, $header, $width);
        $this->setShowIn(self::SHOW_IN_GRID);
        $this->setRenderer('cellButton');
        $this->setSortable(false);
        $this->setColumnType('button');
    }

    public function load($row, $role)
    {
        return null;
    }
}
