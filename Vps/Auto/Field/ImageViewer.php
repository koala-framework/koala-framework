<?php
class Vps_Auto_Field_ImageViewer extends Vps_Auto_Field_Abstract
{
    private $_ruleKey;

    public function __construct($field_name = null, $field_label = null, $ruleKey = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('imageviewer');
        $this->setRuleKey($ruleKey);
    }

    public function setRuleKey($ruleKey)
    {
        $this->_ruleKey = $ruleKey;
        return $this;
    }

    public function load($row)
    {
        $data = array();
        $data['imageUrl'] = $row->getRow()->getFileUrl($this->_ruleKey);
        $data['previewUrl'] = $row->getRow()->getFileUrl($this->_ruleKey, 'thumb');
        return array($this->getFieldName() => $data);
    }
}
