<?php
class Vpc_Basic_Image_Enlarge_Form extends Vpc_Basic_Image_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        if (Vpc_Abstract::getSetting($class, 'hasSmallImageComponent')) {
            $image = Vpc_Abstract_Form::createComponentForm($class, '{0}-imageEnlarge');
            $image->fields->getByName('vps_upload_id')->setFileFieldLabel(trlVps('File (optional)'));
            $this->add(new Vps_Form_Container_FieldSet(trlVps('Small Image')))
                ->add($image);
        }
    }
}
