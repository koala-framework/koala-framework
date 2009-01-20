<?php
class Vps_Controller_Action_Trl_WebEditController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vps_Trl_Model_Web';
    protected $_colNames = array();

    protected function _initFields()
    {

        $config = Zend_Registry::get('config');
        $weblang = $config->webCodeLanguage;

        $this->_form->add(new Vps_Form_Field_ShowField('id', trlVps('Id')));
        $this->_form->add(new Vps_Form_Field_ShowField('context', trlVps('Context')));
        $this->_form->add(new Vps_Form_Field_ShowField($weblang, trlVps($weblang.' Singular')));
        $this->_form->add(new Vps_Form_Field_ShowField($weblang.'_plural', trlVps($weblang.' Plural')));

        $config = Zend_Registry::get('config');
        if ($config->languages) {
            foreach ($config->languages as $lang) {
                if ($lang != $weblang) {
                    $this->_form->add(new Vps_Form_Field_TextField($lang, $lang." ".trlVps("Singular")))->setWidth(400);
                    $this->_colNames[] = $lang;

                    $this->_form->add(new Vps_Form_Field_TextField("$lang._plural", $lang." ".trlVps("Plural")))->setWidth(400);
                    $this->_colNames[] = "$lang._plural";
                }
            }
        }
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
