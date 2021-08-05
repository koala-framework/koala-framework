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
            ->setHelpText(trlKwf('Enter the name (without file extension), the file should get when downloading it.'))
            ->setWidth(300)
            ->setAllowBlank(false);


        $rel = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Relationship attribute')));
        $rel->add(new Kwf_Form_Field_Checkbox('rel_nofollow', trlKwf('no-follow')));
        $rel->add(new Kwf_Form_Field_Checkbox('rel_noopener', trlKwf('no-opener')));
        $rel->add(new Kwf_Form_Field_Checkbox('rel_noreferrer', trlKwf('no-referrer')));
    }
}
