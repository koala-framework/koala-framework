<?php
class Vps_Form_Field_PageSelect extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('pageselect');
    }

    public function load($row, $postData = array())
    {
        $ret = parent::load($row, $postData);

        if (!empty($ret[$this->getFieldName()])) {
            $id = $ret[$this->getFieldName()];
            $ret[$this->getFieldName()] = array('id'=>$id);

            $cmp = Vps_Component_Data_Root::getInstance()->getComponentByDbId(
                $id, array('ignoreVisible'=>true)
            );
            if ($cmp) {
                $ret[$this->getFieldName()]['name'] = $cmp->getTitle();
            } else {
                $ret[$this->getFieldName()]['id'] = null;
                $ret[$this->getFieldName()]['name'] = '';
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
