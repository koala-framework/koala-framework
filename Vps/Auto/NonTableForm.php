<?php
class Vps_Auto_NonTableForm extends Vps_Form_Container_Abstract
{
    private $_name;
    private $_id;

    public function __construct($name = null, $id = null)
    {
        if (!isset($this->fields)) {
            $this->fields = new Vps_Collection_FormFields();
        }
        parent::__construct($name);
        $this->setId($id);
    }

    protected function _getRowByParentRow($parentRow)
    {
        return $this->getRow();
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

    public function getRow()
    {
        return (object)array('id' => $this->getId());
    }
}
