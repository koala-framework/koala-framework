<?php
/**
 * Auswahl wo man nicht reinschreiben kann, so wie eine HTML-Select-Box
 **/
class Vps_Form_Field_Select extends Vps_Form_Field_ComboBox
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);

        $this->setEditable(false);
        $this->setTriggerAction('all');
    }

    public function getTemplateVars($values)
    {
        $ret = parent::getTemplateVars($values);

        $name = $this->getFieldName();
        if (isset($values[$name])) {
            $value = $values[$name];
        } else {
            $value = '';
        }
        //todo: escapen
        $ret['html'] = "<select id=\"$name\" name=\"$name\">";
        //todo: andere values varianten ermöglichen
        //todo: html wählt ersten wert vor-aus - ext galub ich nicht
        //      => sollte sich gleich verhalten.
        $store = $this->_getStoreData();
        if ($this->getShowNoSelection()) {
            array_unshift($store['data'], array('', '('.trlVps('no selection').')'));
        }
        foreach ($store['data'] as $i) {
            $ret['html'] .= '<option value="'.$i[0].'"';
            if ($i[0] == $value) $ret['html'] .= ' selected="selected"';
            $ret['html'] .= '>'.htmlspecialchars($i[1]).'</option>';
        }
        $ret['html'] .= "</select>\n";
        return $ret;
    }
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Select Field'),
            'default' => array(
                'width' => 100
            )
        ));
    }
}
