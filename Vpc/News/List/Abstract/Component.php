<?php
abstract class Vpc_News_List_Abstract_Component extends Vpc_Abstract_Composite_Component
            implements Vpc_Paging_ParentInterface
{
    private $_newsComponent = false;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'component' => array(
                'view' => 'Vpc_News_List_Abstract_View_Component',
                'paging' => 'Vpc_News_List_Abstract_Paging_Component',
                'feed' => 'Vpc_News_List_Abstract_Feed_Component'
            ),
            'class' => 'Vps_Component_Generator_Static'
        );

        $ret['order'] = 'publish_date DESC';
        return $ret;
    }

    public function getNewsComponent()
    {
        if ($this->_newsComponent === false) {
            $this->_newsComponent = $this->_getNewsComponent();
        }
        return $this->_newsComponent;
    }
    abstract protected function _getNewsComponent();

    protected function _selectNews()
    {
        if (!$this->getNewsComponent()) return null;
        $select = $this->getNewsComponent()->getGenerator('detail')
            ->select($this->getNewsComponent());
        $select->where('publish_date <= NOW()');
        if (Vpc_Abstract::getSetting($this->getNewsComponent()->componentClass, 'enableExpireDate')) {
            $select->where('expiry_date >= NOW() OR ISNULL(expiry_date)');
        }
        return $select;
    }

    public function getNews($limit = null, $start = null)
    {
        $select = $this->_selectNews();
        if (!$select) return array();
        if (!$limit && !$start) {
            $l = $this->getData()->getChildComponent('-paging')
                ->getComponent()->getLimit();
            $limit = $l['limit'];
            $start = $l['start'];
        }
        $select->limit($limit, $start);
        $select->order($this->_getSetting('order'));
        $select->group('vpc_news.id');
        return $this->getNewsComponent()->getChildComponents($select);
    }

    public function getPagingCount()
    {
        return $this->_selectNews();
    }
}
