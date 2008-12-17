<?php
class Vpc_Directories_Item_Detail_Related_Component extends Vpc_Abstract
{
    protected $_relatedCache = null;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['linkLimit'] = 3;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['related'] = $this->_getRelatedCompaniesLinks();
        return $ret;
    }

    protected function _getCategoryDirectory()
    {
        return Vps_Component_Data_Root::getInstance()->getComponentByClass(
            'Vpc_Directories_Category_Directory_Component',
            array('subroot' => $this->getData())
        );
    }

    public function hasContent()
    {
        $ret = $this->_getRelatedCompaniesLinks();
        return count($ret) ? true : false;
    }

    /**
     * @return array $relatedEntries
     */
    private function _getRelatedCompaniesLinks()
    {
        if (!is_null($this->_relatedCache)) return $this->_relatedCache;

        $itemRow = $this->getData()->parent->row;

        $categoryDirectory = $this->_getCategoryDirectory();
        $connectTableName = Vpc_Abstract::getSetting(
            $categoryDirectory->componentClass, 'categoryToItemTableName'
        );

        $linkLimit = $this->_getSetting('linkLimit');

        $categoryReference = Vpc_Directories_Category_Detail_List_Component::getTableReferenceData(
            $connectTableName, 'Category'
        );

        $categoryIds = array();
        $categoryIdsRowset = $itemRow->getRow()->findDependentRowset($connectTableName);
        foreach ($categoryIdsRowset as $categoryIdRow) {
            $categoryIds[] = $categoryIdRow->{$categoryReference['itemColumn']};
        }

        if ($categoryIds) {
            $itemReference = Vpc_Directories_Category_Detail_List_Component::getTableReferenceData(
                $connectTableName, 'Item'
            );

            // anzahl holen
            $s = new Zend_Db_Select(Vps_Registry::get('db'));
            $s->from($itemReference['tableName'], $itemReference['itemColumn']);
            $s->where($categoryReference['itemColumn'].' IN('.implode(',', $categoryIds).')');
            $s->order($itemReference['tableName'].'.id ASC');
            $rowset = $s->query()->fetchAll();

            // eigene position ermitteln und rauslöschen
            $selfPos = 0;
            $i = 0;
            foreach ($rowset as $k => $row) {
                if ($row[$itemReference['itemColumn']] == $itemRow->id) {
                    if (!$selfPos) $selfPos = $i;
                    unset($rowset[$k]);
                }
                $i++;
            }
            $rowset = array_values($rowset);
            $categoryRowCount = count($rowset);

            $itemIds = $ret = array();
            if ($categoryRowCount) {
                for ($i = 0; $i < $linkLimit; $i++) {
                    $offset =
                        ($selfPos + ( floor($categoryRowCount / $linkLimit) * $i ))
                        % $categoryRowCount;

                    $itemIds[] = $rowset[$offset][$itemReference['itemColumn']];
                }

                $itemDirectory = $this->getData()->parent->parent;
                $select = $itemDirectory->getGenerator('detail')->select($itemDirectory);
                $select->where('id IN('.implode(',', $itemIds).')');

                $ret = $itemDirectory->getChildComponents($select);
            }
            $this->_relatedCache = $ret;
            return $ret;
        }
        $this->_relatedCache = array();
        return array();
    }

}
