<?php
class Kwc_Basic_ImageParentEditable_ImageFile extends Kwc_Abstract_Image_ImageFile
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('kwc.imageparenteditable.imagefile');
    }
}
