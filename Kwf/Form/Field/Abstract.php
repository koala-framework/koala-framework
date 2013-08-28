<?php
/**
 * Base class for all form fields
 * @package Form
 */
abstract class Kwf_Form_Field_Abstract implements Kwf_Collection_Item_Interface
{
    private $_properties;
    private $_validatorsAdded = false;
    protected $_validators = array();
    private $_data;
    private $_mask;

    public function __construct($fieldName = null, $fieldLabel = null)
    {
        $this->setLabelSeparator(':');
        $this->setName($fieldName);
        $this->setFieldLabel($fieldLabel);
        $this->_init();
    }

    /**
     * Override to add custom initialisation code
     */
    protected function _init()
    {
    }

    public function initFields()
    {
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                $field->initFields();
            }
        }
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'set') {
            if (!isset($arguments[0]) && !is_null($arguments[0])) {
                throw new Kwf_Exception("Missing argument 1 (value)");
            }
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->setProperty($name, $arguments[0]);
        } else if (substr($method, 0, 3) == 'get') {
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->getProperty($name);
        } else {
            throw new Kwf_Exception("Invalid method called: '$method'");
        }
    }

    /**
     * Sets the field name, should be the same as the model column
     */
    public function setName($name)
    {
        if ($name && !preg_match('#^[a-z0-9_\-\[\]]+$#i', $name)) {
            throw new Kwf_Exception("Invalid field name '$name'");
        }
        return $this->__call('setName', array($name));
    }

    protected function _getTrlProperties()
    {
        return array('fieldLabel', 'helpText');
    }

    public function trlStaticExecute($language = null)
    {
        $trl = Kwf_Trl::getInstance();
        foreach ($this->_getTrlProperties() as $property) {
            $trlStaticData = $this->getProperty($property);
            $this->setProperty(
                $property,
                $trl->trlStaticExecute($trlStaticData, $language)
            );
        }

        foreach ($this->getValidators() as $v) {
            $v->setTranslator(new Kwf_Trl_ZendAdapter($language));
        }

        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                $field->trlStaticExecute($language);
            }
        }
    }

    /**
     * Set any property supported by ExtJS for this field
     *
     * Alternatively use setFooBar() to set property fooBar
     */
    public function setProperty($name, $value)
    {
        if (is_null($value)) {
            unset($this->_properties[$name]);
        } else {
            $this->_properties[$name] = $value;
        }
        return $this;
    }

    /**
     * Get any property supported by ExtJS for this field
     *
     * Alternatively use getFooBar() to get property fooBar
     */
    public function getProperty($name)
    {
        if (isset($this->_properties[$name])) {
            return $this->_properties[$name];
        } else {
            return null;
        }
    }

    public function setProperties(array $properties)
    {
        foreach ($properties as $k=>$value) {
            if (is_string($k) && $k != 'id') {
                $fn = 'set'.str_replace(' ','',ucwords(str_replace('_', ' ', $k)));
                call_user_func(array($this, $fn), $value);
            }
        }
    }

    public function getMetaData($model)
    {
        $ret = $this->_properties;
        if (isset($ret['name'])) {
            $ret['name'] = $this->getFieldName();
        }
        if (isset($ret['hiddenName']) && $this->getNamePrefix()) {
            $ret['hiddenName'] = $this->getNamePrefix() . '_' . $ret['hiddenName'];
        }
        if (isset($ret['namePrefix'])) unset($ret['namePrefix']);
        if (isset($ret['save'])) unset($ret['save']);
        return $ret;
    }

    public function getFrontendMetaData()
    {
        return array();
    }

    public function load($row, $postData = array())
    {
        $ret = array();
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                if ($this->_processChildren('load', $field, $row, $postData)) {
                    $ret = array_merge($ret, $field->load($row, $postData));
                }
            }
        }
        return $ret;
    }

    protected function _processChildren($method, $childField, $row, $postData)
    {
        return true;
    }

    public function processInput($row, $postData)
    {
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                if ($this->_processChildren('processInput', $field, $row, $postData)) {
                    $postData = $field->processInput($row, $postData);
                }
            }
        }
        return $postData;
    }

    public function validate($row, $postData)
    {
        $ret = array();
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                if ($this->_processChildren('validate', $field, $row, $postData)) {
                    $ret = array_merge($ret, $field->validate($row, $postData));
                }
            }
        }
        return $ret;
    }

    public function prepareSave($row, $postData)
    {
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                if ($this->_processChildren('prepareSave', $field, $row, $postData)) {
                    $field->prepareSave($row, $postData);
                }
            }
        }
    }

    public function save($row, $postData)
    {
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                if ($this->_processChildren('save', $field, $row, $postData)) {
                    $field->save($row, $postData);
                }
            }
        }
    }

    public function afterSave($row, $postData)
    {
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                if ($this->_processChildren('afterSave', $field, $row, $postData)) {
                    $field->afterSave($row, $postData);
                }
            }
        }
    }

    public function delete(Kwf_Model_Row_Interface $row)
    {
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                $field->delete($row);
            }
        }
    }

    public function getName()
    {
        if (isset($this->_properties['name'])) {
            return $this->getProperty('name');
        } else if (isset($this->_properties['hiddenName'])) {
            return $this->getHiddenName();
        } else {
            return null;
        }
    }

    /**
     * returns the fully qualified field name, different to getName when using form in form
     *
     * @return string
     */
    public function getFieldName()
    {
        $ret = $this->getName();
        if ($this->getNamePrefix()) {
            $ret = $this->getNamePrefix() . '_' . $ret;
        }
        return $ret;
    }

    /**
     * returns a field by its name
     *
     * @return Kwf_Form_Field_Abstract
     */
    public function getByName($name)
    {
        if ($this->getName() == $name) {
            return $this;
        } else {
            return null;
        }
    }

    public function hasChildren()
    {
        return false;
    }

    public function getChildren()
    {
        return array();
    }

    public function getValidators()
    {
        if (!$this->_validatorsAdded) {
            $this->_addValidators();
            $this->_validatorsAdded = true;
        }
        return $this->_validators;
    }
    /**
     * @param Zend_Validate_Interface $v Der validator
     * @param string $key Um zB einen Validator zu finden und durch einen
     *                    anderen zu ersetzen, zB bei {@link Kwf_Form_Field_Checkbox}
     */
    public function addValidator(Zend_Validate_Interface $v, $key = null)
    {
        if (is_null($key)) {
            $this->_validators[] = $v;
        } else {
            $this->_validators[$key] = $v;
        }
        return $this;
    }

    public function clearValidators()
    {
        $this->_validators = array();
    }

    /**
     * Add validators to the field here, called in prepareSave
    **/
    protected function _addValidators()
    {
    }

    public function getData()
    {
        if (!isset($this->_data)) {
            $this->setData(new Kwf_Data_Table());
        }
        return $this->_data;
    }

    public function setData(Kwf_Data_Interface $data)
    {
        $this->_data = $data;
        $data->setFieldname($this->getName());
        return $this;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = array();
        $ret['item'] = $this;
        $ret['mask'] = $this->_mask;
        return $ret;
    }

    public function mask($name)
    {
        $this->_mask = $name;
    }

    /**
     * @internal
     */
    public function toDebug($indent=0)
    {
        $ind = str_repeat(' ', $indent*4);
        $ret = '';
        $ret .= '<pre style="margin:0">';
        $ret .= "$ind<strong>".get_class($this)."</strong>";
        $c = get_class($this);
        while ($c = get_parent_class($c)) {
            $ret .= " -&gt; $c";
        }
        $ret .= "\n";
        foreach ($this->_properties as $n=>$v) {
            if (is_object($v)) {
                if (method_exists($v, '__toString')) {
                    $v = $v->__toString();
                } else {
                    $v = "(object) ".get_class($v);
                }
            }
            $ret .= "$ind  $n: $v\n";
        }
        $children = '';
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                $children .= $field->toDebug($indent+1);
            }
        }
        if ($this->hasChildren()) {
            $ret .= "{$ind}childs(".count($this->getChildren())."):\n";
        }
        $ret .= '</pre>';
        $ret .= $children;
        return $ret;
    }

    /**
     * Sets the field label
     */
    public function setFieldLabel($value)
    {
        return $this->setProperty('fieldLabel', $value);
    }

    /**
     * Sets the label separator
     *
     * defaults to ':'
     */
    public function setLabelSeparator($value)
    {
        return $this->setProperty('labelSeparator', $value);
    }
}
