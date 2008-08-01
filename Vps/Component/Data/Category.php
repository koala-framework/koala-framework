<?php
class Vps_Component_Data_Category extends Vps_Component_Data_Root
{
    public function __construct($id, $name)
    {
        $config = array(
            'componentId' => $id,
            'name' => $name
        );
        parent::__construct($config);
    }
}
?>