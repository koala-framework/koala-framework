<?php
abstract class Vpc_News_List_Abstract_Component extends Vpc_Abstract_Composite_Component
{
    private $_newsComponent = false;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'component' => array(
                'view' => 'Vpc_News_List_Abstract_View_Component',
                'feed' => 'Vpc_News_List_Abstract_Feed_Component'
            ),
            'class' => 'Vps_Component_Generator_Static'
        );
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
        return $this->getNewsComponent()->getComponent()->selectNews();
    }

    public function selectNews()
    {
        return $this->_selectNews();
    }

    public function getNews($limit = null, $start = null)
    {
        $select = $this->_selectNews();
        if (!$select) return array();
        if ($limit) {
            $select->limit($limit, $start);
        }
        $select->group('vpc_news.id');
        return $this->getNewsComponent()->getChildComponents($select);
    }
}
