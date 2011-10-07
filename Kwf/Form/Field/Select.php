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
        $this->setEmptyMessage(trlVpsStatic('Please select a value'));
    }

    //setHideIfNoValues

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix);

        $store = $this->_getStoreData();

        if (!$store['data'] && $this->getHideIfNoValues()) {
            $ret['html'] = '';
            $ret['item'] = null;
            return $ret;
        }

        $name = $this->getFieldName();
        $value = isset($values[$name]) ? $values[$name] : $this->getDefaultValue();

        $onchange = '';
        if ($this->getSubmitOnChange())
            $onchange= " onchange=\"this.form.submit();\"";

        //todo: escapen
        $ret['id'] = str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $style = '';
        if ($this->getWidth()) {
            $style = " style=\"width: ".$this->getWidth()."px\"";
        }
        $ret['html'] = "<select id=\"$ret[id]\" name=\"$name$fieldNamePostfix\"$onchange$style>";
        //todo: andere values varianten ermöglichen
        //todo: html wählt ersten wert vor-aus - ext galub ich nicht
        //      => sollte sich gleich verhalten.
        if ($this->getShowNoSelection()) {
            $emptyText = '('.$this->getEmptyText().')';
            array_unshift($store['data'], array('', $emptyText));
        }
        foreach ($store['data'] as $i) {
            $ret['html'] .= '<option value="'.$i[0].'"';
            if ($i[0] == $value) $ret['html'] .= ' selected="selected"';
            $ret['html'] .= '>'.htmlspecialchars($i[1]).'</option>';
        }
        $ret['html'] .= "</select>\n";
        if ($this->getSubmitOnChange())
            $ret['html'] .= '<input type="submit" value="»" />';
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
