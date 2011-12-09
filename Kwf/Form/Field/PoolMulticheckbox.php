<?php
/**
 * @package Form
 */
class Kwf_Form_Field_PoolMulticheckbox extends Kwf_Form_Field_MultiCheckbox
{
    public function __construct($tableName = null, $title = null)
    {
        parent::__construct($tableName, $title);
    }

    public function setPool($pool)
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Pool');
        $select = $model->select()
            ->whereEquals('pool', $pool)
            ->whereEquals('visible', 1)
            ->order('pos', 'ASC');
        $this->setValues($model->getRows($select));
        return $this;
    }
}
