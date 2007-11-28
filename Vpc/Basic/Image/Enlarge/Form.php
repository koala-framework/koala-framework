<?ph
class Vpc_Basic_Image_Enlarge_Form extends Vpc_Basic_Image_For

    public function __construct($class, $pageId = null, $componentKey = null
    
        parent::__construct($class, $pageId, $componentKey)

        $class = Vpc_Abstract::getSetting($class, 'enlargeClass')
        $image = new Vpc_Basic_Image_Form($class, $pageId, $componentKey . '-1')
        $image->fields->getByName('vps_upload_id')->setFileFieldLabel('File (optional)')
        $this->add(new Vps_Auto_Container_FieldSet('Enlarged Image')
            ->setCheckboxToggle(true
            ->setCheckboxName('enlarge'
            ->add($image)
    
