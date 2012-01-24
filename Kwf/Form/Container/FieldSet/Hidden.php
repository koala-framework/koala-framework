<?php
/**
 * @internal
 * @package Form
 */
class Kwf_Form_Container_FieldSet_Hidden extends Kwf_Form_Field_Hidden
{
    public function getMetaData($model)
    {
        return 'hidden';
    }

    protected function _getValueFromPostData($postData)
    {
        $fieldName = $this->getFieldName();
        if (!isset($postData[$fieldName])) $postData[$fieldName] = $this->getDefaultValue();
        return (bool)$postData[$fieldName];
    }

    protected function _processLoaded($value)
    {
        return (int)$value;
    }

}
