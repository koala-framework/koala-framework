<?php
class Vpc_Basic_Image_Enlarge_Form extends Vpc_Basic_Image_Form
{
    public function __construct($class, $id)
    {
        parent::__construct($class, $id);
        $childId = $id;
        $childId['component_key'] .= '-1';

        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        $image = new Vpc_Basic_Image_Form($classes['enlarge'], $childId);
        $image->fields->getByName('vps_upload_id')->setFileFieldLabel('File (optional)');
        $this->add(new Vps_Auto_Container_FieldSet('Enlarged Image'))
            ->setCheckboxToggle(true)
            ->setCheckboxName('enlarge')
            ->add($image);
    }
}
