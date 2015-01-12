<?php
class Kwc_Basic_DownloadTag_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->setXtype('Kwc.Basic.DownloadTag');
        $this->fields->add(new Kwf_Form_Field_File('File', trlKwf('File')))
            ->setDirectory('BasicDownloadTag')
            ->setAllowOnlyImages(false)
            ->setAllowBlank(false);
        $this->fields->add(new Kwf_Form_Field_TextField('filename', trlKwf('Filename')))
            ->setVtype('alphanum')
            ->setAutoFillWithFilename('filename') //um es beim MultiFileUpload und im JavaScript zu finde
            ->setHelpText(hlpKwf('kwf_download_filename'))
            ->setWidth(300)
            ->setAllowBlank(false);
    }
}
