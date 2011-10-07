<?php
class Vps_Grid_Column_RtrIndicator extends Vps_Grid_Column
{
    public function __construct($dataIndex = null, $header = '', $width = 30)
    {
        parent::__construct($dataIndex, $header, $width);
        $this->setShowIn(self::SHOW_IN_GRID);
        $this->setRenderer('booleanRtr');
        $this->setSortable(false);
        $this->setType('boolean');
    }

    public function load($row, $role)
    {
        return null;
    }
}
