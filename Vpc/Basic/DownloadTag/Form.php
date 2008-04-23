<?php
class Vpc_Basic_DownloadTag_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Vps_Form_Field_TextField('filename', trlVps('Filename')))
            ->setAllowBlank(false)
            ->setVtype('alphanum');
        $this->fields->add(new Vps_Form_Field_File('vps_upload_id', trlVps('File')))
            ->setDirectory('BasicDownloadTag');
    }
}
