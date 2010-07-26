<?php
class Vpc_Basic_DownloadTag_Trl_Form_OriginalData extends Vps_Data_Trl_OriginalComponent
{
    public function load($row)
    {
        $pk = $row->getModel()->getPrimaryKey();
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($row->$pk, array('ignoreVisible'=>true));
        // das ist nötig weil bei der übersetzung bei den link-cards
        // natürlich gleich alle geladen werden und im chained dann zB ein
        // download-tag drin ist und kein externer / etc.
        if (is_instance_of($c->chained->componentClass, 'Vpc_Basic_DownloadTag_Component')) {
            return parent::load($row);
        } else {
            return '';
        }
    }
}

class Vpc_Basic_DownloadTag_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Own Download')));
        $fs->setCheckboxToggle(true);
        $fs->setCheckboxName('own_download');
        $fs->add(Vpc_Abstract_Form::createChildComponentForm($this->getClass(), '-download', 'download'));

        $this->add(new Vps_Form_Field_ShowField('original_filename', trlVps('Original Filename')))
            ->setData(new Vpc_Basic_DownloadTag_Trl_Form_OriginalData('filename'));
    }
}
