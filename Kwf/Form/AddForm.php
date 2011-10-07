<?php
class Vps_Form_AddForm extends Vps_Form
{
    public function __construct($name = null)
    {
        $this->setIdTemplate('{id}');
        $this->setCreateMissingRow(true);
        parent::__construct($name);
    }

}
