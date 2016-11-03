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
            if ($data instanceof Kwf_Model_Rowset_Interface) {
                foreach ($data as $row) {
                    if ($row->id == $ret[$this->getFieldName()]) {
                        $ret[$this->getFieldName()] = $row->__toString();
                    }
                }
            } else if (is_array($data)) {
                if (isset($data[0]) && is_array($data[0])) {
                        $oldData = $data;
                        $data = array();
                    foreach ($oldData as $d) {
                        $data[$d[0]] = $d[1];
                    }
                }
                if ($ret[$this->getFieldName()] === false || $ret[$this->getFieldName()] === null) {
                    $ret[$this->getFieldName()] = null;
                } else if (!$ret[$this->getFieldName()]) {
                    $ret[$this->getFieldName()] = null;
                } else {
                    $ret[$this->getFieldName()] = $data[$ret[$this->getFieldName()]];
                }
            }
        } else {
            $reference = $this->getReference();
            if ($reference) {
                if ($row->getParentRow($reference)) {
                    if ($referenceField = $this->getReferenceField()) {
                        $ret[$this->getFieldName()] = $row->getParentRow($reference)->$referenceField;
                    } else {
                        $ret[$this->getFieldName()] = $row->getParentRow($reference)->__toString();
                    }
                } else {
                    $ret[$this->getFieldName()] = null;
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

    public function trlStaticExecute($language = null)
    {
        parent::trlStaticExecute($language);
        $trl = Kwf_Trl::getInstance();

        $values = $this->getValues();
        if (is_array($values)) {
            foreach ($values as $k => $v) {
                $newKey = $k;
                $newValue = $v;
                if (is_string($k)) $newKey = $trl->trlStaticExecute($k, $language); //TODO key nicht (immer) übersetzen
                if (is_string($v)) $newValue = $trl->trlStaticExecute($v, $language);
                else if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if (is_string($v2)) {
                            $newValue[$k2] = $trl->trlStaticExecute($v2, $language);
                        }
                    }
                }

                unset($values[$k]);
                $values[$newKey] = $newValue;
            }
            $this->setProperty('values', $values);
        }
    }
}
