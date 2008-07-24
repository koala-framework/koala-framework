<?php
class Vpc_News_List_Abstract_View_Component extends Vpc_Abstract_Composite_Component
            implements Vpc_Paging_ParentInterface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['paging'] = 'Vpc_News_List_Abstract_View_Paging_Component';
        $ret['cssClass'] = 'webStandard';
        $ret['viewCache'] = false;
        return $ret;
    }
    protected function _selectNews()
    {
        return $this->getData()->parent->getComponent()->selectNews();
    }

    protected function _getNews()
    {
        $select = $this->_selectNews();
        if (!$select) return array();
        $this->getData()->getChildComponent('-paging')
            ->getComponent()->limitSelect($select);
        $select->group('vpc_news.id');
        $ret = $this->getData()->parent->getComponent()->getNewsComponent()
                    ->getChildComponents($select);
        foreach ($ret as $new) {
            $this->getData()->parent->getComponent()->getNewsComponent()
                ->getComponent()->modifyNewsData($new);
        }
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['news'] = $this->_getNews();
        $ret['pagingVisible'] = $this->getData()->getChildComponent('-paging')
                                    ->getComponent()->getPagingVisible();
        $ret['allNews'] = $this->getData()->parent->getComponent()->getNewsComponent();
        return $ret;
    }
    public function getPagingCount()
    {
        return $this->_selectNews();
    }
}
