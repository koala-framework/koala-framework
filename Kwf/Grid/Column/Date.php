<?php
class Kwf_Grid_Column_Date extends Kwf_Grid_Column
{
    public function __construct($dataIndex = null, $header = null, $width = 80)
    {
        if (is_null($header)) $header = trlKwf('Date');
        parent::__construct($dataIndex, $header, $width);
        $this->setType('date');
        $this->setRenderer('localizedDate');
        $this->setDateFormat('Y-m-d');
    }

    public function load($row, $role, $info)
    {
        $ret = parent::load($row, $role, $info);
        $ret = substr($ret, 0, 10);
        if ($ret == '0000-00-00') $ret = null;
        //todo: datum formatieren wenn export; übersetzung berücksichtigen
        return $ret;
    }
}
