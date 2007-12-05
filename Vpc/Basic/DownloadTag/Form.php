<?php
class Vpc_Basic_DownloadTag_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $id)
    {
        parent::__construct($class, $id);
        $this->fields->add(new Vps_Auto_Field_TextField('filename', 'Filename'))
            ->setAllowBlank(false)
            ->setVtype('alphanum');
        $this->fields->add(new Vps_Auto_Field_File('vps_upload_id', 'File'))
            ->setDirectory('BasicDownloadTag');
    }
}
