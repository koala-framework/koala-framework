<?php
class Kwc_Basic_DownloadTag_Trl_Form_OriginalData extends Kwf_Data_Trl_OriginalComponent
{
    public function load($row)
    {
        $pk = $row->getModel()->getPrimaryKey();
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->$pk, array('ignoreVisible'=>true));
        // das ist nötig weil bei der übersetzung bei den link-cards
        // natürlich gleich alle geladen werden und im chained dann zB ein
        // download-tag drin ist und kein externer / etc.
        if ($c && is_instance_of($c->chained->componentClass, 'Kwc_Basic_DownloadTag_Component')) {
            return parent::load($row);
        } else {
            return '';
        }
    }
}

class Kwc_Basic_DownloadTag_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Own Download')));
        $fs->setCheckboxToggle(true);
        $fs->setCheckboxName('own_download');
        $fs->add(Kwc_Abstract_Form::createChildComponentForm($this->getClass(), '-download', 'download'));

        $this->add(new Kwf_Form_Field_ShowField('original_filename', trlKwf('Original Filename')))
            ->setData(new Kwc_Basic_DownloadTag_Trl_Form_OriginalData('filename'));
    }
}
