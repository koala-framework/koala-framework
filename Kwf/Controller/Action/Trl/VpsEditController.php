<?php
class Vps_Controller_Action_Trl_VpsEditController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vps_Trl_Model_Vps';
    protected $_colNames = array();

    protected function _initFields()
    {
        $lang = $this->_getLanguage();
        $this->_form->add(new Vps_Form_Field_ShowField('context', trlVps('Context')));
        $this->_form->add(new Vps_Form_Field_ShowField($lang, $lang.' '.trlVps('Singular')));
        $this->_form->add(new Vps_Form_Field_ShowField($lang.'_plural', $lang.' '.trlVps('Plural')));

        $langs = Vps_Controller_Action_Trl_VpsController::getLanguages();
        if ($langs) {
            foreach ($langs as $lang) {
                if ($lang != $this->_getLanguage()) {
                    $this->_form->add(new Vps_Form_Field_TextField($lang, $lang." ".trlVps("Singular")))->setWidth(400);
                    $this->_colNames[] = $lang;

                    $this->_form->add(new Vps_Form_Field_TextField($lang."_plural", $lang." ".trlVps("Plural")))->setWidth(400);
                    $this->_colNames[] = $lang."_plural";
                }
            }
        }
    }

    protected function _getLanguage()
    {
        return 'en';
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        foreach ($this->_colNames as $colName)
        if (!$row->{$colName}) {
            unset($row->{$colName});
        }
    }


}
