<?php
class Kwc_Basic_LinkTag_Intern_Events extends Kwc_Abstract_Events
{
    private $_pageIds;

    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onRecursiveUrlChanged'
        );
        return $ret;
    }

    public function onRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($event->componentId);
        foreach ($this->_getDbIds($component->dbId) as $dbId) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $dbId));
            if ($component->isPage) {
                $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $dbId));
            }
        }
    }

    public function onOwnRowUpdate(Kwf_Component_Event_Row_Abstract $event)
    {
        parent::onOwnRowUpdate($event);
        $this->_pageIds = null;
    }

    private function _getDbIds($targetId)
    {
        if (!$this->_pageIds) {
            $ids = array();
            $model = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->_class, 'ownModel'));
            foreach ($model->export(Kwf_Model_Abstract::FORMAT_ARRAY) as $row) {
                $target = $row['target'];
                if (!isset($ids[$target])) $ids[$target] = array();
                $ids[$target][] = $row['component_id'];
            }
            $this->_pageIds = $ids;
        }
        $ret = array();
        foreach ($this->_pageIds as $pageId => $dbIds) {
            if (substr($pageId, 0, strlen($targetId)) == $targetId) {
                $ret = array_merge($ret, $dbIds);
            }
        }
        return $ret;
    }

}
