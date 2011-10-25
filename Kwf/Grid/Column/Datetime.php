<?php
class Kwf_Grid_Column_Datetime extends Kwf_Grid_Column
{
    public function __construct($dataIndex = null, $header = null, $width = 110)
    {
        if (is_null($header)) $header = trlKwf('Date');
        parent::__construct($dataIndex, $header, $width);
        $this->setType('date');
        $this->setRenderer('localizedDatetime');
        $this->setDateFormat('Y-m-d H:i:s');
    }
}