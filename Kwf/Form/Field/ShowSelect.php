<?php
/**
 * @package Form
 */
class Kwf_Form_Field_ShowSelect extends Kwf_Form_Field_ShowField
{
    public function load($row, $postData = array())
    {
        $ret = parent::load($row, $postData);
        $data = $this->getValues();
        if ($data) {
            if ($data instanceof Kwf_Db_Table_Rowset_Abstract
                || $data instanceof Kwf_Model_Rowset_Interface
            ) {
                foreach ($data as $row) {
                    if ($row->id == $ret[$this->getName()]) {
                        $ret[$this->getName()] = $row->__toString();
                    }
                }
            } else if (is_array($data)) {
                if ($ret[$this->getName()] === false || $ret[$this->getName()] === null) {
                    $ret[$this->getName()] = null;
                } else if (!$ret[$this->getName()]) {
                    $ret[$this->getName()] = null;
                } else {
                    $ret[$this->getName()] = $data[$ret[$this->getName()]];
                }
            }
        } else {
            $reference = $this->getReference();
            if ($reference) {
                if ($referenceField = $this->getReferenceField()) {
                    $ret[$this->getName()] = $row->getParentRow($reference)->$referenceField;
                } else {
                    $ret[$this->getName()] = $row->getParentRow($reference)->__toString();
                }
            }
        }
        return $ret;
    }
    public function setValues($values)
    {
        $ret = parent::setValues($values);
        return $ret;
    }
    public function setReference($reference)
    {
        $ret = parent::setReference($reference);
        return $ret;
    }
    public function setReferenceField($referenceField)
    {
        $ret = parent::setReferenceField($referenceField);
        return $ret;
    }
}
