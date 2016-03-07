<?php
class Kwf_Form_Field_DateSelect extends Kwf_Form_Field_SimpleAbstract
{
    private $_language = null;
    public function trlStaticExecute($language = null)
    {
        parent::trlStaticExecute($language);
        $this->_language = $language;
    }
    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Kwf_Validate_Date(array('outputFormat' => trlKwf('yyyy-mm-dd'))));
    }

    public function processInput($row, $postData)
    {
        $fieldName = $this->getFieldName();
        if (isset($postData[$fieldName.'_day']) && isset($postData[$fieldName.'_month']) && isset($postData[$fieldName.'_year'])) {
            if (!$postData[$fieldName.'_year'] || !$postData[$fieldName.'_month'] || !$postData[$fieldName.'_day']) {
                $postData[$fieldName] = null;
            } else {
                $postData[$fieldName] = $postData[$fieldName.'_year'].'-'.$postData[$fieldName.'_month'].'-'.$postData[$fieldName.'_day'];
            }

        }
        return $postData;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        $name = $this->getFieldName();
        $valueYear = 0;
        $valueMonth = 0;
        $valueDay = 0;

        $value = isset($values[$name]) ? $values[$name] : $this->getDefaultValue();
        if ($value) {
            $value = strtotime($value);
            if ($value) {
                $valueYear = (int)date('Y', $value);
                $valueMonth = (int)date('m', $value);
                $valueDay = (int)date('d', $value);
            }
        }
        $kwfTrl = Kwf_Trl::getInstance();

        $ret['id'] = $idPrefix.str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $ret['html'] = "<select name=\"{$name}_day\">";
        $ret['html'] .= "<option value=\"\">{$kwfTrl->trlKwf('Day', array(), $this->_language)}</option>";
        for ($i = 1; $i <= 31; $i++) {
            $v = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ret['html'] .= "<option value=\"{$v}\"";
            if ($i == $valueDay) $ret['html'] .= ' selected="selected"';
            $ret['html'] .= ">{$i}</option>";
        }
        $ret['html'] .= "</select>";
        $ret['html'] = '<div class="outerSelect day">'.$ret['html'].'</div><div class="outerSelect month">';

        $months = array(
            $kwfTrl->trlKwf('January', array(), $this->_language),
            $kwfTrl->trlKwf('February', array(), $this->_language),
            $kwfTrl->trlKwf('March', array(), $this->_language),
            $kwfTrl->trlKwf('April', array(), $this->_language),
            $kwfTrl->trlKwf('May', array(), $this->_language),
            $kwfTrl->trlKwf('June', array(), $this->_language),
            $kwfTrl->trlKwf('July', array(), $this->_language),
            $kwfTrl->trlKwf('August', array(), $this->_language),
            $kwfTrl->trlKwf('September', array(), $this->_language),
            $kwfTrl->trlKwf('October', array(), $this->_language),
            $kwfTrl->trlKwf('November', array(), $this->_language),
            $kwfTrl->trlKwf('December', array(), $this->_language)
        );
        $ret['html'] .= "<select name=\"{$name}_month\">";
        $ret['html'] .= "<option value=\"\">{$kwfTrl->trlKwf('Month', array(), $this->_language)}</option>";
        for ($i = 1; $i <= 12; $i++) {
            $v = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ret['html'] .= "<option value=\"{$v}\"";
            if ($i == $valueMonth) $ret['html'] .= ' selected="selected"';
            $ret['html'] .= ">{$months[$i-1]}</option>";
        }
        $ret['html'] .= "</select>";
        $ret['html'] = $ret['html'].'</div><div class="outerSelect year">';

        $ret['html'] .= "<select name=\"{$name}_year\">";
        $ret['html'] .= "<option value=\"\">{$kwfTrl->trlKwf('Year', array(), $this->_language)}</option>";
        for ($i = date('Y'); $i >= 1900; $i--) {
            $ret['html'] .= "<option value=\"{$i}\"";
            if ($i == $valueYear) $ret['html'] .= ' selected="selected"';
            $ret['html'] .= ">{$i}</option>";
        }
        $ret['html'] .= "</select>";
        $ret['html'] = $ret['html'].'</div>';
        return $ret;
    }
}

