<?php
class Kwf_Component_Cache_Url_Mysql extends Kwf_Component_Cache_Url_Abstract
{
    protected $_models;

    public function __construct()
    {
        $this->_models = array (
            'url' => 'Kwf_Component_Cache_Url_Mysql_Model',
        );
    }

    /**
     * @return Kwf_Model_Abstract
     */
    public function getModel($type = 'url')
    {
        if (!isset($this->_models[$type])) return null;
        if (is_string($this->_models[$type])) {
            $this->_models[$type] = Kwf_Model_Abstract::getInstance($this->_models[$type]);
        }
        return $this->_models[$type];
    }

    public function load($cacheUrl)
    {
        $cacheId = 'url-'.$cacheUrl;
        $ret = Kwf_Cache_Simple::fetch($cacheId);
        if ($ret) {
            $ret = Kwf_Component_Data::kwfUnserialize($ret);
        }
        return $ret;

    }

    public function save($cacheUrl, Kwf_Component_Data $data)
    {
        $cacheId = 'url-'.$cacheUrl;

        Kwf_Cache_Simple::add($cacheId, $data->kwfSerialize());

        $this->getModel()->import(Kwf_Model_Abstract::FORMAT_ARRAY,
            array(array(
                'url' => $cacheUrl,
                'page_id' => $data->componentId,
                'expanded_page_id' => $data->getExpandedComponentId()
            )), array('replace'=>true, 'skipModelObserver'=>true));
    }

    public function delete(array $constraints)
    {
        $ors = array();
        foreach ($constraints as $i) {
            if (substr($i['value'], -1) == '%') {
                $ors[] = new Kwf_Model_Select_Expr_Like($i['field'], $i['value']);
            } else {
                $ors[] = new Kwf_Model_Select_Expr_Equal($i['field'], $i['value']);
            }
        }
        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Or($ors));
        $rows = $this->getModel()->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select);
        foreach ($rows as $row) {
            $cacheId = 'url-'.$row['url'];
            Kwf_Cache_Simple::delete($cacheId);
        }
    }

    public function clear()
    {
        $select = new Kwf_Model_Select();
        $rows = $this->getModel()->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select);
        foreach ($rows as $row) {
            $cacheId = 'url-'.$row['url'];
            Kwf_Cache_Simple::delete($cacheId);
        }
        $this->getModel()->deleteRows($select);
    }
}
