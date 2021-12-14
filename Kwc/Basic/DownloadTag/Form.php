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

        // cards container erstellen und zu form hinzufügen
        $cards = $this->add(new Kwf_Form_Container_Cards('open_type', trlKwf('Open in')));
        $cards->getCombobox()->setAllowBlank(false);
        $cards->getCombobox()->setDefaultValue('self');
        $card = $cards->add();
            $card->setTitle(trlKwf('Same window'));
            $card->setName('self');
        $card = $cards->add();
            $card->setTitle(trlKwf('New window'));
            $card->setName('blank');

        $this->add(new Kwf_Form_Field_Select('content_disposition', trlKwf('Target options')))
            ->setValues(array(
                'attachment' => trlKwf('Download'),
                'inline' => trlKwf('Show on page'),
            ))
            ->setAllowBlank(false)
            ->setDefaultValue('attachment');

        $this->add(new Kwf_Form_Field_Checkbox('rel_noindex', trlKwf('no-index')));
    }
}
