<?p
class Vpc_Basic_DownloadTag_Form extends Vps_Auto_Vpc_Fo

    public function __construct($class, $pageId = null, $componentKey = nul
   
        parent::__construct($class, $pageId, $componentKey
        $this->fields->add(new Vps_Auto_Field_TextField('filename', 'Filename'
            ->setAllowBlank(fals
            ->setVtype('alphanum'
        $this->fields->add(new Vps_Auto_Field_File('vps_upload_id', 'File'
            ->setDirectory('BasicDownloadTag'
   

