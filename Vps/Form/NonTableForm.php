<?php
class Vps_Form_NonTableForm extends Vps_Form_Container_Abstract
{
    private $_id;
    private $_rows;

    public function __construct($name = null)
    {
        if (!isset($this->fields)) {
            $this->fields = new Vps_Collection_FormFields();
        }
        parent::__construct($name);
    }
    protected function _getIdByParentRow($parentRow)
    {
        $id = $this->getId();
        if ($this->getIdTemplate()) {
            if (!$parentRow) {
                throw new Vps_Exception("Form has an idTemplate set - so getRow required a parentRow as first argument");
            }
            $pk = $parentRow->getModel()->getPrimaryKey();
            $id = $parentRow->$pk;
            if (!$id) {
                return null;
            }
            $id = str_replace('{0}', $id, $this->getIdTemplate());
            if (preg_match_all('#{([a-z0-9_]+)}#', $id, $m)) {
                foreach ($m[1] as $i) {
                    if (!isset($parentRow->$i)) {
                        throw new Vps_Exception("Column '$i' as specified in idTemplate doesn't exist in parentRow");
                    }
                    $id = str_replace('{'.$i.'}', $parentRow->$i, $id);
                }
            }
        }
        return $id;
    }

    protected function _getRowByParentRow($parentRow)
    {
        $key = $parentRow ? $parentRow->getInternalId() : 0;
        if (!isset($this->_rows[$key])) {
            $id = $this->_getIdByParentRow($parentRow);
            $model = new Vps_Model_FnF();
            $this->_rows[$key] = $model->createRow(array('id' => $id));
        }
        if ($this->_rows[$key] instanceof Vps_Model_FnF_Row) {
            $this->_rows[$key]->id = $this->_getIdByParentRow($parentRow);
        }
        return $this->_rows[$key];
    }

    public function setName($name)
    {
        $this->fields->setFormName($name); //damit prefixName der Felder nachträglich angepasst wird
        parent::setName($name);
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }
    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        unset($ret['idTemplate']);
        return $ret;
    }

    public function getModel()
    {
        $this->getProperty('model');
    }

    public function setModel($value)
    {
        return $this->setProperty('model', $value);
    }
}
