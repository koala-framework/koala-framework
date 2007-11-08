<?php
class Vpc_Table extends Vps_Db_Table
{
    public function findRow($id)
    {
        $parts = Vpc_Abstract::parseId($id);
        return $this->find($parts['dbId'], $parts['componentKey'])->current();
    }
    public function createRow($class, array $data = array())
    {
        $defaultValues = Vpc_Abstract::getSetting($class, 'default');
        if (is_array($defaultValues)) {
            $data = array_merge($data, $defaultValues);
        }
        return parent::createRow($data);
    }
}