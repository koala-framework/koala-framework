<?php
class Vps_Grid_Column_Visible extends Vps_Grid_Column_Checkbox
{
    public function __construct($dataIndex = 'visible', $header = '', $width = 30)
    {
        parent::__construct($dataIndex, $header, $width);
        $this->setRenderer('booleanTickCross');
        $this->setHeaderIcon(new Vps_Asset('visible'));
        $this->setTooltip('Visibility');
        $this->setEditor(new Vps_Form_Field_Checkbox());
    }
}
