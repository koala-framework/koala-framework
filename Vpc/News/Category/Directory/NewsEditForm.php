<?php
class Vpc_News_Category_Directory_NewsEditForm extends Vps_Auto_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);

        $tablename = Vpc_Abstract::getSetting($class, 'tablename');
        $this->_table = new $tablename(array('componentClass'=>$class));

        $this->add(new Vps_Auto_Field_PoolMulticheckbox('Vpc_News_Categories_NewsToPoolModel', 'Categories'))
             ->setPool(Vpc_Abstract::getSetting($class, 'pool'));
    }

    public function delete($parentRow)
    {
        $this->_row = $parentRow;
        return parent::delete($parentRow);
    }
    public function load($parentRow)
    {
        $this->_row = $parentRow;
        return parent::load($parentRow);
    }
    public function prepareSave($parentRow, $postData)
    {
        $this->_row = $parentRow;
        return parent::prepareSave($parentRow, $postData);
    }

    public function save($parentRow, $postData)
    {
        $this->_row = $parentRow;
        return parent::save($parentRow, $postData);
    }}
