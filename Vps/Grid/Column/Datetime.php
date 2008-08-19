<?php
class Vps_Grid_Column_Datetime extends Vps_Grid_Column
{
    public function __construct($dataIndex = null, $header = 'Datetime', $width = 110)
    {
        parent::__construct($dataIndex, $header, $width);
        $this->setType('date');
        $this->setRenderer('localizedDatetime');
        $this->setDateFormat('Y-m-d H:i:s');
    }
}