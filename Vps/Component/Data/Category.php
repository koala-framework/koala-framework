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
    
    public function getChildComponents($constraints = array())
    {
        $constraints['type'] = $this->componentId;
        return parent::getChildComponents($constraints);
    }
}
?>