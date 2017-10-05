<?php
/**
 * Auswahl wo man nicht reinschreiben kann, so wie eine HTML-Select-Box
 *
 * @package Form
 **/
class Kwf_Form_Field_Select extends Kwf_Form_Field_ComboBox
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);

        $this->setEditable(false);
        $this->setTriggerAction('all');
        $this->setParenthesesAroundEmptyText(true);
        $this->setEmptyMessage(trlKwfStatic('Please select a value'));
    }

    //setHideIfNoValues

    protected function _addValidators()
    {
        parent::_addValidators();

        //if hideIfNoValues and there are no values remove empty validator
        if ($this->getHideIfNoValues() && isset($this->_validators['notEmpty'])) {
            $store = $this->_getStoreData();
            if (!$store['data']) {
                unset($this->_validators['notEmpty']);
            }
        }
    }

    public function validate($row, $postData)
    {
        $ret = parent::validate($row, $postData);

        $data = $this->_getValueFromPostData($postData);

        if (!$this->getShowNoSelection() && is_null($data)) {
            //regardless of allowBlank a select *always* needs a selection
            $ignoreEmpty = true;
            if ($this->getHideIfNoValues()) {
                $store = $this->_getStoreData();
                if (!$store['data']) {
                    $ignoreEmpty = true;
                }
            }
            if (!$ignoreEmpty) {
                $ret[] = array(
                    'message' => $this->getEmptyText(),
                    'field' => $this
                );
            }
        }

        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);

        $store = $this->_getStoreData();

        if (!$store['data'] && $this->getHideIfNoValues()) {
            $ret['html'] = '';
            $ret['item'] = null;
            return $ret;
        }

        $name = $this->getFieldName();
        $value = isset($values[$name]) ? $values[$name] : $this->getDefaultValue();

        //todo: escapen
        $ret['id'] = $idPrefix.str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $style = '';
        if ($this->getWidth()) {
            $style = " style=\"width: ".$this->getWidth()."px\"";
        }
        $cssClass = $this->getCls();
        $ret['html'] = "<select id=\"$ret[id]\" name=\"$name$fieldNamePostfix\"$style class=\"$cssClass\">";
        //todo: andere values varianten ermöglichen
        //todo: html wählt ersten wert vor-aus - ext galub ich nicht
        //      => sollte sich gleich verhalten.
        if ($this->getShowNoSelection()) {
            $emptyText = $this->getEmptyText();
            if ($emptyText && $this->getParenthesesAroundEmptyText()) $emptyText = '('.$emptyText.')';
            array_unshift($store['data'], array('', $emptyText));
        }

        $disabledValues = $this->getDisabledValues();
        foreach ($store['data'] as $i) {
            $ret['html'] .= '<option value="'.$i[0].'"';
            if ($disabledValues && in_array($i[0], $disabledValues)) {
                $ret['html'] .= ' disabled="disabled"';
            }
            if (!is_null($value) && $i[0] == $value) $ret['html'] .= ' selected="selected"';
            $ret['html'] .= '>'.Kwf_Util_HtmlSpecialChars::filter($i[1]).'</option>';
        }
        $ret['html'] .= "</select>\n";
        if ($this->getSubmitOnChange())
            $ret['html'] .= '<input class="submit" type="submit" value="»" />';
        $ret['html'] = '<div class="outerSelect">'.$ret['html'].'</div>';
        return $ret;
    }


    /**
     * Enable Parentheses around empty text
     *
     * Only used in frontend when emptyText is set.
     *
     * @param bool
     * @return $this
     */
    public function setParenthesesAroundEmptyText($value)
    {
        return $this->setProperty('parenthesesAroundEmptyText', $value);
    }
}
