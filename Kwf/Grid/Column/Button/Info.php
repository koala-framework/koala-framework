<?php
class Vps_Grid_Column_Button_Info extends Vps_Grid_Column_Button
{
    public function __construct($dataIndex = null, $header = null, $width = 30)
    {
        if (is_null($header)) $header = trlVps('Info');
        parent::__construct($dataIndex, $header, $width);
        $this->setButtonIcon(new Vps_Asset('information.png'));
    }
}
