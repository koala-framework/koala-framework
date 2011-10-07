<?php
class Vpc_Directories_CategoryTree_Directory_Row extends Vps_Model_Tree_Row
{
    public function __toString()
    {
        return $this->name;
    }

    protected function _delete()
    {
        if (count($this->getChildNodes())) {
            throw new Vps_ClientException(
                trlVps("This category can't be deleted, because there exist sub-categories in it.")
            );
        }
    }

    public function getRecursiveChildCategoryIds()
    {
        return $this->getRecursiveIds();
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