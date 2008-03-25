<?php
class Vpc_Basic_Image_Enlarge_Form extends Vpc_Basic_Image_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);
        $childId = $id . '-1';

        if (Vpc_Abstract::getSetting($class, 'hasSmallImageComponent')) {
            $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
            $image = new Vpc_Basic_Image_Form($classes['smallImage'], $childId);
            $image->fields->getByName('vps_upload_id')->setFileFieldLabel(trlVps('File (optional)'));
            $this->add(new Vps_Auto_Container_FieldSet('Small Image (optional)'))
                ->setCheckboxToggle(true)
                ->setCheckboxName(trlVps('enlarge'))
                ->add($image);
        }
    }
}
