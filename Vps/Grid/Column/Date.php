<?php
class Vps_Grid_Column_Date extends Vps_Grid_Column
{
    public function __construct($dataIndex = null, $header = 'Date', $width = 70)
    {
        parent::__construct($dataIndex, $header, $width);
        $this->setType('date');
        $this->setRenderer('localizedDate');
        $this->setDateFormat('Y-m-d');
    }

    public function load($row, $role)
    {
        $ret = parent::load($row, $role);
        $ret = substr($ret, 0, 10);
        //todo: datum formatieren wenn export; übersetzung berücksichtigen
        return $ret;
    }
}
