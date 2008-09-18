<?php
class Vpc_Basic_Image_Enlarge_Form extends Vpc_Basic_Image_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        if (Vpc_Abstract::getSetting($class, 'hasSmallImageComponent')) {
            $image = Vpc_Abstract_Form::createChildComponentForm($class, '-smallImage');
            $image->fields->getByName('vps_upload_id')->setFileFieldLabel(trlVps('File (optional)'));
            $this->add(new Vps_Form_Container_FieldSet(trlVps('Small Image')))
                ->add($image);
        }
    }
}
