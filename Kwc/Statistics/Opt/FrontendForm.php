<?php
class Kwc_Statistics_Opt_FrontendForm extends Kwc_Abstract_FrontendForm
{
    protected function _init()
    {
        $this->setModel(new Kwf_Model_FnF(
            array('data' => array(array('id' => 1, 'opt' => Kwf_Statistics::getUserOptValue())))
        ));
        $this->setId(1);
        $select = new Kwf_Form_Field_Select('opt', trlKwfStatic('Cookie Setting'));
        $values = array(
            Kwf_Statistics::OPT_IN => trlKwfStatic('Allow the use of cookies'),
            Kwf_Statistics::OPT_OUT => trlKwfStatic('Do not allow the use of cookies')
        );
        $select->setValues($values)->setAllowBlank(false);
        if (!Kwf_Statistics::issetUserOptValue()) {
            $select->setShowNoSelection(true)->setEmptyText(trlKwfStatic('Please select a value'));
        }
        $this->add($select);
        parent::_init();
    }

    protected function _afterSave($row)
    {
        Kwf_Statistics::setUserOptValue($row->opt);
    }
}
