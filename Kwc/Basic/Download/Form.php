<?php
class Kwc_Basic_Download_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $form = Kwc_Abstract_Form::createChildComponentForm($class, '-downloadTag');
        $this->add($form);

        $this->add(new Kwf_Form_Field_TextField('infotext', trlKwf('Descriptiontext')))
            ->setWidth(300)
            ->setAutoFillWithFilename('filenameWithExt') //um es beim MultiFileUpload zu finde
            ->setHelpText(hlpKwf('kwc_download_linktext'))
            ->setAllowBlank(false);
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);

        if (!$row->infotext) {
            $row->infotext = $this->getByName('downloadTag')
                ->getRow($row)
                ->getParentRow('File')
                ->filename;
        }
    }
}
