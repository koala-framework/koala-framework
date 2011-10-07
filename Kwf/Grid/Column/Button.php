<?php
class Vps_Grid_Column_Button extends Vps_Grid_Column
{
    public function __construct($dataIndex = null, $header = '', $width = 30)
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
