<?php
/**
 * @package Form
 */
class Kwf_Form_AddForm extends Kwf_Form
{
    public function __construct($name = null)
    {
        $this->setIdTemplate('{id}');
        $this->setCreateMissingRow(true);
        parent::__construct($name);
    }

}
