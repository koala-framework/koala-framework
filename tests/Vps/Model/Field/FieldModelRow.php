<?php
class Vps_Model_Field_FieldModelRow extends Vps_Model_Field_Row
{
    static public $counts = array(
        'beforeUpdate' => 0,
        'beforeInsert' => 0,
        'beforeSave' => 0,
        'beforeDelete' => 0,
        'afterUpdate' => 0,
        'afterInsert' => 0,
        'afterSave' => 0,
        'afterDelete' => 0
    );

    protected function _beforeUpdate()
    {
        parent::_beforeUpdate();
        self::$counts['beforeUpdate']++;
    }

    protected function _beforeInsert()
    {
        parent::_beforeInsert();
        self::$counts['beforeInsert']++;
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        self::$counts['beforeSave']++;
    }

    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        self::$counts['beforeDelete']++;
    }

    protected function _afterUpdate()
    {
        parent::_afterUpdate();
        self::$counts['afterUpdate']++;
    }

    protected function _afterInsert()
    {
        parent::_afterInsert();
        self::$counts['afterInsert']++;
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        self::$counts['afterSave']++;
    }

    protected function _afterDelete()
    {
        parent::_afterDelete();
        self::$counts['afterDelete']++;
    }
}