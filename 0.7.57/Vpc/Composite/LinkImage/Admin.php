<?php
class Vpc_Composite_LinkImage_Admin extends Vpc_Abstract_Composite_Admin
{

    public function setup()
    {
        parent::setup();
        $this->createFormTable('vpc_composite_linkimage', array());
    }
}
