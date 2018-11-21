<?php
class Kwf_Grid_Column_Button extends Kwf_Grid_Column
{
    const BUTTON_VISIBLE = 'visible';
    const BUTTON_INVISIBLE = 'invisible';

    public function __construct($dataIndex = null, $header = '', $width = 30)
    {
        parent::__construct($dataIndex, $header, $width);
        $this->setShowIn(self::SHOW_IN_GRID);
        $this->setRenderer('cellButton');
        $this->setSortable(false);
        $this->setColumnType('button');
        $this->setData(new Kwf_Data_Button());
    }
}
