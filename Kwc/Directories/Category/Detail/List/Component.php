<?php
class Kwc_Directories_Category_Detail_List_Component extends Kwc_Directories_List_Component
{
    public function getSelect()
    {
        $class = $this->getData()->parent->parent->componentClass;
        $childReference =
            Kwc_Abstract::hasSetting($class, 'childReferenceName') ?
            Kwc_Abstract::getSetting($class, 'childReferenceName') :
            'Categories';

        $select = parent::getSelect();
        $s = new Kwf_Model_Select();
        $s->whereEquals('category_id', $this->getData()->parent->id);
        $select->where(new Kwf_Model_Select_Expr_Child_Contains($childReference, $s));

        return $select;
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        return self::_getParentItemDirectoryClasses($directoryClass, 3);
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent->parent;
    }

    //wurder ursprünglich hier verwendet was jedoch auf Expr_Child_Contains umgeschireben wurde
    static public function getTableReferenceData($relationModel, $rule/* = 'Item'*/)
    {
        if (is_string($relationModel)) {
            $relationModel = Kwf_Model_Abstract::getInstance($relationModel);
        }

        $reference = $relationModel->getReference($rule);
        $dataModel = Kwf_Model_Abstract::getInstance($reference['refModelClass']);
        while ($dataModel instanceof Kwf_Model_Proxy) {
            $dataModel = $dataModel->getProxyModel();
        }

        return array(
            'tableName' => $relationModel->getProxyModel()->getTableName(),
            'itemColumn' => $reference['column'],
            'refItemColumn' => $dataModel->getPrimaryKey(),
            'refTableName' => $dataModel->getTableName()
        );
    }
}
