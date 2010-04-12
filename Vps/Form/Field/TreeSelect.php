<?php
class Vps_Form_Field_TreeSelect extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('treeselect');
    }

    // -> setReference('Category') muss gesetzt werden, damit der inhalt beim
    // laden stimmt.
    // -> setDisplayField setzt ein Feld, das beim Laden und auch nach dem
    // Auswählen eines Eintrags verwendet wird. (standard ist der .js nodeText)

    // -> setCutNodes(2) kann verwendet werden, um die ersten X ebenen im Text
    // abzuschneiden (nachdem ein wert gewählt wurde). Standard ist das auf 1,
    // was im normalfall den Root ausblendet.
    // Muss auch im model korrekt implementiert sein

    // wenn man zu faul ist das model zu erben damit man die korrekte reference
    // einstellt, kann man auch ein Data erstellen, dass den wert nach dem laden
    // korrekt darstellt

    public function load($row, $postData = array())
    {
        $ret = parent::load($row, $postData);

        $ref = $this->getReference();
        if ($ref && $row && !empty($ret[$this->getFieldName()])) {
            $id = $ret[$this->getFieldName()];
            $parentRow = $row->getParentRow($ref);

            if ($parentRow) {
                $displayField = $this->getDisplayField();
                if ($displayField) {
                    $ret[$this->getFieldName()] = array(
                        'id' => $id,
                        'name' => $parentRow->{$displayField}
                    );
                } else if ($parentRow instanceof Vps_Model_Tree_Row_Interface) {
                    $ret[$this->getFieldName()] = array(
                        'id' => $id,
                        'name' => $parentRow->getTreePath()
                    );
                } else {
                    $ret[$this->getFieldName()] = array(
                        'id' => $id,
                        'name' => $parentRow->__toString()
                    );
                }
            } else {
                $ret[$this->getFieldName()] = array(
                    'id' => $id,
                    'name' => ''
                );
            }
        }

        return $ret;
    }

    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if ($ret == '' || $ret == 'null') $ret = null;
        return $ret;
    }
}
