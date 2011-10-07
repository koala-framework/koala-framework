<?php
interface Kwf_Component_Abstract_Admin_Interface_DependsOnRow
{
    public function getComponentsDependingOnRow(Kwf_Model_Row_Interface $row);
}
