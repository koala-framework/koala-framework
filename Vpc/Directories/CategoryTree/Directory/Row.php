<?php
class Vpc_Directories_CategoryTree_Directory_Row extends Vps_Db_Table_Row_Abstract
{
    public function __toString()
    {
        return $this->name;
    }

    protected function _delete()
    {
        if (count($this->findDependentRowset(get_class($this->getTable())))) {
            throw new Vps_ClientException(
                trlVps("This category can't be deleted, because there exist sub-categories in it.")
            );
        }
    }

    public function getTreePath($separator = ' » ')
    {
        $path = $this->__toString();
        $upperRow = $this;
        while (!is_null($upperRow = $upperRow->findParentRow(get_class($this->getTable())))) {
            $path = $upperRow->__toString().$separator.$path;
        }
        return $path;
    }

    public function __isset($name)
    {
        if ($name == 'name_path') {
            return true;
        } else {
            return parent::__isset($name);
        }
    }

    public function __get($name)
    {
        if ($name == 'name_path') {
            return $this->getTreePath();
        } else {
            return parent::__get($name);
        }
    }
}