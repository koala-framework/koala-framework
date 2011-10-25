<?php
class Kwf_Grid_Column_Visible extends Kwf_Grid_Column_Checkbox
{
    public function __construct($dataIndex = 'visible', $header = '', $width = 30)
    {
        parent::__construct($dataIndex, $header, $width);
        $this->setRenderer('booleanTickCross');
        $this->setHeaderIcon(new Kwf_Asset('visible'));
        $this->setTooltip('Visibility');
        $this->setEditor(new Kwf_Form_Field_Checkbox());
    }
}
