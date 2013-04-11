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
                        $toStringField = $data->getModel()->getToStringField();
                        $ret[$this->getName()] = $row->$toStringField;
                    }
                }
            } else if (is_array($data)) {
                $ret[$this->getName()] = $data[$ret[$this->getName()]];
            }
        } else {
            $reference = $this->getReference();
            if ($reference) {
                if ($referenceField = $this->getReferenceField()) {
                    $ret[$this->getName()] = $row->getParentRow($reference)->$referenceField;
                } else {
                    $toStringField = $row->getParentRow($reference)->getModel()->getToStringField();
                    $ret[$this->getName()] = $row->getParentRow($reference)->$toStringField;
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
