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
            $card = $cards->add();
                $card->setTitle(trlKwf('Same window'));
                $card->setName('self');
            $card = $cards->add();
                $card->setTitle(trlKwf('New window'));
                $card->setName('blank');
            if (Kwc_Abstract::getSetting($class, 'hasPopup')) {
                $card = $cards->add();
                    $card->setTitle(trlKwf('Popup'));
                    $card->setName('popup');

                    $card->add(new Kwf_Form_Field_TextField('width', 'Width'))
                        ->setDefaultValue(400)
                        ->setAllowBlank(false)
                        ->setVtype('alphanum');
                    $card->add(new Kwf_Form_Field_TextField('height', 'Height'))
                        ->setDefaultValue(400)
                        ->setAllowBlank(false)
                        ->setVtype('alphanum');
                    $card->add(new Kwf_Form_Field_Checkbox('menubar', 'Menubar'));
                    $card->add(new Kwf_Form_Field_Checkbox('toolbar', 'Toolbar'));
                    $card->add(new Kwf_Form_Field_Checkbox('locationbar', 'Locationbar'));
                    $card->add(new Kwf_Form_Field_Checkbox('statusbar', 'Statusbar'));
                    $card->add(new Kwf_Form_Field_Checkbox('scrollbars', 'Scrollbars'));
                    $card->add(new Kwf_Form_Field_Checkbox('resizable', 'Resizable'));
            }


        $this->add(new Kwf_Form_Field_Select('content_disposition', trlKwf('Target options')))
            ->setValues(array(
                'attachment' => trlKwf('Download'),
                'inline' => trlKwf('Show on page'),
            ))
            ->setAllowBlank(false)
            ->setDefaultValue('attachment');

        $rel = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Relationship attribute')));
        $rel->add(new Kwf_Form_Field_Checkbox('rel_nofollow', trlKwf('no-follow')));
        $rel->add(new Kwf_Form_Field_Checkbox('rel_noopener', trlKwf('no-opener')));
        $rel->add(new Kwf_Form_Field_Checkbox('rel_noreferrer', trlKwf('no-referrer')));
    }
}
