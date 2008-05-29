<?php
class Vpc_Basic_DownloadTag_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->setXtype('Vpc.Basic.DownloadTag');
        $this->fields->add(new Vps_Form_Field_TextField('filename', trlVps('Filename')))
            ->setAllowBlank(false)
            ->setVtype('alphanum')
            ->setIsFilename(true) //um es im JavaScript zu finden
            ->setHelpText(hlpVps('vps_download_filename'));
        $this->fields->add(new Vps_Form_Field_File('vps_upload_id', trlVps('File')))
            ->setDirectory('BasicDownloadTag')
            ->setAllowBlank(false)
            ->setAllowOnlyImages(false);
    }
}
