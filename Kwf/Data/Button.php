<?php
class Kwf_Data_Button extends Kwf_Data_Empty
{
    public function load($row, array $info = array())
    {
        return Kwf_Grid_Column_Button::BUTTON_VISIBLE;
    }
}
