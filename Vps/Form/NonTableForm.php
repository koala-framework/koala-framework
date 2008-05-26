<?php
class Vps_Form_NonTableForm extends Vps_Form_Container_Abstract
{
    private $_id;

    public function __construct($name = null, $id = null)
    {
        if (!isset($this->fields)) {
            $this->fields = new Vps_Collection_FormFields();
        }
        parent::__construct($name);
        if (!is_null($id)) $this->setId($id);
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
        $id = $this->_getIdByParentRow($parentRow);
        $model = new Vps_Model_FnF();
        return $model->createRow(array('id' => $id));
    }

    public function setName($name)
    {
        $this->fields->setFormName($name); //damit prefixName der Felder nachträglich angepasst wird
        $this->setProperty('name', $name);
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }
    public function getMetaData()
    {
        $ret = parent::getMetaData();
        unset($ret['idTemplate']);
        return $ret;
    }
}
