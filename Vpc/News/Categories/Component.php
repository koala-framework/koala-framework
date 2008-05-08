<?php

class Vpc_News_Categories_Component extends Vpc_Abstract implements Vpc_News_Interface_Component
{

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'         => 'Vpc_News_Model',
            'childComponentClasses' => array(
                'details'       => 'Vpc_News_Categories_Category_Component'
            ),
            'pool'              => 'Newskategorien'
        ));
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/News/Categories/Plugins.js';
        return $ret;
    }

    public function getNewsComponent()
    {
        $returnComponent = $this->getParentComponent();
        while (!$returnComponent instanceof Vpc_News_Component) {
            $returnComponent = $returnComponent->getParentComponent();
        }
        return $returnComponent;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['categories'] = array();

        $where = array(
            'pool = ?' => $this->_getSetting('pool')
        );
        if (!$this->showInvisible()) {
            $where[] = 'visible = 1';
        }

        $table = new Vps_Dao_Pool();
        $rowset = $table->fetchAll($where, 'pos ASC');
        foreach ($rowset as $row) {
            $detailpage = $this->getPageFactory()->getChildPageByRow($row);

            $data = $row->toArray();
            $data['href'] = $this->getPageCollection()->getUrl($detailpage);
            $ret['categories'][] = $data;
        }
        return $ret;
    }

}
