<?php
class Vps_Controller_Action_Trl_WebBasicController extends Vps_Controller_Action_Auto_Grid
{
    protected $_modelName = "Vps_Trl_Model_Web";
    protected $_buttons = array('save');
    protected $_sortable = false;
    protected $_defaultOrder = 'id';
    protected $_paging = 30;
    protected $_columns;
    protected $_colNames = array();

    protected function _initColumns()
    {
        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width'=>80
        );

        $config = Zend_Registry::get('config');
        $weblang = $config->webCodeLanguage;

        $this->_columns->add(new Vps_Grid_Column('id'));
        $this->_columns->add(new Vps_Grid_Column('context', trlVps('Context')))
            ->setWidth(50);

        $languages = array();
        $languages[] = $weblang;
        $plural = array();
        $role = $this->_getAuthData()->role;
        $user_lang = $this->_getAuthData()->language;

        //defintion der zu übersetzenden sprachen
        if ($role == 'admin') {
            if ($config->languages) {
                foreach($config->languages as $language) {
                    if ($language != $weblang) {
                        $languages[] = $language;
                    }
                }
            }
        } else {
            if ($user_lang != $weblang)
                $languages[] = $user_lang;
        }

        //ausgabe der einzahl Felder
        foreach ($languages as $lang) {
            //Singular
            if ($lang == $weblang) {
                $this->_columns->add(new Vps_Grid_Column($lang, trlVps("$lang Singular")))
                    ->setWidth(200)
                    ->setRenderer('notEditable');
                $this->_colNames[] = $lang;
            } else {
                if ($lang == $user_lang || $role == 'admin') {
                    $this->_columns->add(new Vps_Grid_Column($lang, trlVps("$lang Singular")))
                        ->setEditor(new Vps_Form_Field_TextField())
                        ->setWidth(200);;
                    $this->_colNames[] = $lang;
                }
            }
        }

        //Ausgabe der Plural felder
        foreach ($languages as $lang) {
            //Plural
            if ($lang == $weblang) {
                $this->_columns->add(new Vps_Grid_Column($lang."_plural", $lang.trlVps(" Plural")))
                    ->setWidth(200)
                    ->setRenderer('notEditable');
                $this->_colNames[] = $lang."_plural";
            } else {
                if ($lang == $user_lang || $role == 'admin') {
                    $this->_columns->add(new Vps_Grid_Column($lang."_plural", $lang.trlVps(" Plural")))
                        ->setEditor(new Vps_Form_Field_TextField())
                        ->setWidth(200);
                    $this->_colNames[] = $lang."_plural";
                }
            }
       }

        parent::_initColumns();
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeSave($row, $submitRow);
        foreach ($this->_colNames as $colName)
        if (!$row->{$colName}) {
            unset($row->{$colName});
        }
    }
}