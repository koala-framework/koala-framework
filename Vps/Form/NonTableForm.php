<?php
class Vps_Form_NonTableForm extends Vps_Form_Container_Abstract
{
    private $_name;
    private $_id;
    private $_idTemplate;

    public function __construct($name = null, $id = null)
    {
        if (!isset($this->fields)) {
            $this->fields = new Vps_Collection_FormFields();
        }
        parent::__construct($name);
        if (!is_null($id)) $this->setId($id);
    }

    protected function _getRowByParentRow($parentRow)
    {
        $id = $this->getId();
        if ($this->_idTemplate && $parentRow instanceof Vps_Model_Db_Row) {
            $info = $parentRow->getRow()->getTable()->info();
            if (isset($info['primary'][1])) {
                $id = $parentRow->{$info['primary'][1]};
                $id = str_replace('{0}', $id, $this->_idTemplate);
            }
        }
        
        $model = new Vps_Model_FnF();
        return $model->createRow(array('id' => $id));
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        $this->_name = $name;
        $this->fields->setFormName($name); //damit prefixName der Felder nachträglich angepasst wird
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function setIdTemplate($idTemplate)
    {
        $this->_idTemplate = $idTemplate;
        return $this;
    }
    
    public function getIdTemplate()
    {
        return $this->_idTemplate;
    }
}
