<?php
class Vpc_Directories_Item_Directory_ControllerEditButton extends Vps_Grid_Column_Button
{
    public function load($row, $role)
    {
        if ($row->getModel()->hasColumn('component') &&
            $this->getEditComponent() != $row->component
        ) {
            return self::BUTTON_INVISIBLE;
        }
        return parent::load($row, $role);
    }
}
