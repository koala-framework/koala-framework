<?php
class Vpc_Basic_Text_Image_Form extends Vpc_Basic_Image_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);

        //felder ausblenden, werden im rte per drag+drop ge�ndert
        if (isset($this->fields['width'])) {
            unset($this->fields['width']);
        }
        if (isset($this->fields['height'])) {
            unset($this->fields['height']);
        }
    }
}
