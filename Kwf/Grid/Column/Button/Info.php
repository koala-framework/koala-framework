<?php
class Kwf_Grid_Column_Button_Info extends Kwf_Grid_Column_Button
{
    public function __construct($dataIndex = null, $header = null, $width = 30)
    {
        if (is_null($header)) $header = trlKwf('Info');
        parent::__construct($dataIndex, $header, $width);
        $this->setButtonIcon(new Kwf_Asset('information.png'));
    }
}
