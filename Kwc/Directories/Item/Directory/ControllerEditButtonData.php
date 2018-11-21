<?php
class Kwc_Directories_Item_Directory_ControllerEditButtonData extends Kwf_Data_Button
{
    public function load($row, array $info = array())
    {
        if ($row->getModel()->hasColumn('component') &&
            $this->getEditComponent() != $row->component
        ) {
            return Kwf_Grid_Column_Button::BUTTON_INVISIBLE;
        }
        return parent::load($row, $info);
    }
}
