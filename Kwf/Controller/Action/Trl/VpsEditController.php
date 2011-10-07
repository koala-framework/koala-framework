<?php
class Kwf_Controller_Action_Trl_KwfEditController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Kwf_Trl_Model_Kwf';
    protected $_colNames = array();

    protected function _initFields()
    {
        $lang = $this->_getLanguage();
        $this->_form->add(new Kwf_Form_Field_ShowField('context', trlKwf('Context')));
        $this->_form->add(new Kwf_Form_Field_ShowField($lang, $lang.' '.trlKwf('Singular')));
        $this->_form->add(new Kwf_Form_Field_ShowField($lang.'_plural', $lang.' '.trlKwf('Plural')));

        $langs = Kwf_Controller_Action_Trl_KwfController::getLanguages();
        if ($langs) {
            foreach ($langs as $lang) {
                if ($lang != $this->_getLanguage()) {
                    $this->_form->add(new Kwf_Form_Field_TextField($lang, $lang." ".trlKwf("Singular")))->setWidth(400);
                    $this->_colNames[] = $lang;

                    $this->_form->add(new Kwf_Form_Field_TextField($lang."_plural", $lang." ".trlKwf("Plural")))->setWidth(400);
                    $this->_colNames[] = $lang."_plural";
                }
            }
        }
    }

    protected function _getLanguage()
    {
        return 'en';
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        foreach ($this->_colNames as $colName)
        if (!$row->{$colName}) {
            unset($row->{$colName});
        }
    }


}
