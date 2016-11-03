<?php
class Kwc_Directories_Item_Directory_ControllerEditButton extends Kwf_Grid_Column_Button
{
    public function load($row, $role, $info)
    {
        if ($row->getModel()->hasColumn('component') &&
            $this->getEditComponent() != $row->component
        ) {
            return self::BUTTON_INVISIBLE;
        }
        return parent::load($row, $role, $info);
    }
}
