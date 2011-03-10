<?php
class Vpc_Basic_Download_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $form = Vpc_Abstract_Form::createChildComponentForm($class, '-downloadTag');
        $this->add($form);

        $this->add(new Vps_Form_Field_TextField('infotext', trlVps('Descriptiontext')))
            ->setWidth(300)
            ->setHelpText(hlpVps('vpc_download_linktext'))
            ->setAllowBlank(false);
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
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
