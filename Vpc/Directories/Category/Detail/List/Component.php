<?php
class Vpc_Directories_Category_Detail_List_Component extends Vpc_Directories_List_Component
{
    public function getSelect()
    {
        $class = $this->getData()->parent->parent->componentClass;
        $childReference =
            Vpc_Abstract::hasSetting($class, 'childReferenceName') ?
            Vpc_Abstract::getSetting($class, 'childReferenceName') :
            'Categories';

        $select = parent::getSelect();
        $s = new Vps_Model_Select();
        $s->whereEquals('category_id', $this->getData()->parent->id);
        $select->where(new Vps_Model_Select_Expr_Child_Contains($childReference, $s));

        return $select;
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent->parent;
    }

    //wurder ursprünglich hier verwendet was jedoch auf Expr_Child_Contains umgeschireben wurde
    static public function getTableReferenceData($relationModel, $rule/* = 'Item'*/)
    {
        if (is_string($relationModel)) {
            $relationModel = Vps_Model_Abstract::getInstance($relationModel);
        }

        $reference = $relationModel->getReference($rule);
        $dataModel = Vps_Model_Abstract::getInstance($reference['refModelClass']);
        while ($dataModel instanceof Vps_Model_Proxy) {
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
