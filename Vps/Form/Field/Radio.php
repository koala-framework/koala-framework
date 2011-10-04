<?php
/**
 * Auswahl wo man nicht reinschreiben kann, so wie eine HTML-Select-Box
 **/
class Vps_Form_Field_Radio extends Vps_Form_Field_ComboBox
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('radiogroup');
        $this->setOutputType('horizontal');
        $this->setEmptyMessage(trlVpsStatic('Please choose an option'));
    }

    // $this->setOutputType($type)
    //    $type = 'vertical', otherwise horizontal

    // setColumns()

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        unset($ret['store']);
        unset($ret['editable']);
        unset($ret['triggerAction']);
        if (isset($ret['outputType']) && $ret['outputType'] == 'vertical') {
            unset($ret['outputType']);
            $ret['vertical'] = true;
        } else {
            $ret['vertical'] = false;
        }
        $store = $this->_getStoreData();
        if (!isset($store['data'])) {
            throw new Vps_Exception("No data set for radio field '{$this->getName()}'");
        }
        foreach ($store['data'] as $d) {
            $id = $d[0];
            $value = $d[1];
            $ret['items'][] = array(
                'name' => $this->getFieldName(),
                'boxLabel' => $value,
                'inputValue' => $id
            );
        }
        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix);

        $name = $this->getFieldName();
        $value = isset($values[$name]) ? $values[$name] : $this->getDefaultValue();

        $ret['id'] = str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $store = $this->_getStoreData();
        if ($this->getShowNoSelection()) {
            array_unshift($store['data'], array('', '('.trlVps('no selection').')'));
        }
        $ret['html'] = '<div class="vpsFormFieldRadio vpsFormFieldRadio'.ucfirst($this->getOutputType()).'">';
        $k = 0;
        foreach ($store['data'] as $i) {
            $ret['html'] .= '<span class="value'.htmlspecialchars(ucfirst($i[0])).'">';
            $ret['html'] .= '<input type="radio" class="radio" id="'.$ret['id'].++$k.'" '
                .'name="'.$name.$fieldNamePostfix.'" value="'.htmlspecialchars($i[0]).'"';
            if ($value === $i[0] || (!is_null($value) && $i[0] == $value)) {
                $ret['html'] .= ' checked="checked"';
            }
            $ret['html'] .= ' /> <label for="'.$ret['id'].$k.'">'.htmlspecialchars($i[1]).'</label>';
            $ret['html'] .= '</span>';
        }
        $ret['html'] .= '</div>';
        return $ret;
    }
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Radio Buttons'),
            'default' => array(
                'width' => 100
            )
        ));
    }
}
