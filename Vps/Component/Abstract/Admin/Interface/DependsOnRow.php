<?php
interface Vps_Component_Abstract_Admin_Interface_DependsOnRow
{
    public function getComponentsDependingOnRow(Vps_Model_Row_Interface $row);
}
