<?php
class Vpc_News_Component extends Vpc_News_List_Abstract_Component implements Vpc_News_Interface_Component
{
    public $content;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'News.List';
        $ret['componentIcon'] = new Vps_Asset('newspaper');
        $ret['tablename'] = 'Vpc_News_Model';
        $ret['childComponentClasses']['details'] ='Vpc_News_Details_Component';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/News/Panel.js';
        return $ret;
    }

    public function getNews($limit = 15, $start = null)
    {
        $where = array(
            'component_id = ?' => $this->getId(),
            'publish_date <= NOW()',
            'expiry_date >= NOW()'
        );

        if (!$this->showInvisible()) {
            $where['visible = 1'] = '';
        }

        $rows = $this->getTable()->fetchAll($where, 'publish_date DESC', $limit, $start);

        return $rows;
    }

    public function getNewsCount()
    {
        //todo, ist von oben kopiert - wird aber sowiso mit treecache neu gemacht
        $select = $this->getTable()->getAdapter()->select();
        $select->from('vpc_news', array('count' => 'COUNT(*)'))
            ->where('component_id = ?', $this->getId())
            ->where('publish_date <= NOW()')
            ->where('expiry_date >= NOW()');
        if (!$this->showInvisible()) {
            $select->where('visible = 1');
        }
        $r = $select->query()->fetchAll();
        return $r[0]['count'];
    }

    public function getTemplateVars()
    {
        return parent::getTemplateVars();
    }

}
