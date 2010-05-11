<?php
class Vpc_Advanced_Team_Member_Data_Model extends Vps_Component_FieldModel
{
    protected $_rowClass = 'Vpc_Advanced_Team_Member_Data_Row';
    public function __toString()
    {
        if (!empty($this->name)) {
            return $this->name;
        }
        return '';
    }
}
