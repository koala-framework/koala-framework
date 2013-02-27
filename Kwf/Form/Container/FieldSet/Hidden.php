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

    public function processInput($row, $postData)
    {
        $fieldName = $this->getFieldName();
        if (isset($postData[$fieldName.'-post'])) {
            $postData[$fieldName] = (int)isset($postData[$fieldName]);
        }
        return $postData;
    }

    protected function _processLoaded($value)
    {
        return (int)$value;
    }

}
