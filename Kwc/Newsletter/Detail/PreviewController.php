<?php
class Kwc_Newsletter_Detail_PreviewController extends Kwc_Mail_PreviewController
{
    protected function _getRecipient()
    {
        $select = new Kwf_Model_Select();
        $rs = $this->_getMailComponent()->getRecipientSources();
        $recipientId = $this->_getParam('recipientId');
        if (!$recipientId) {
            $source = reset($rs);
            if (!isset($source['select'])) $source['select'] = new Kwf_Model_Select();
            $model = Kwf_Model_Abstract::getInstance($source['model']);
            $row = $model->getRow($source['select']);
            $recipientId = $row->id;
        }
        $select->whereEquals('id', $recipientId);
        $subscribeModelKey = $this->_getParam('subscribeModelKey');
        if (!$subscribeModelKey) $subscribeModelKey = current(array_keys($rs));
        $model = $rs[$subscribeModelKey]['model'];
        $row = Kwf_Model_Abstract::getInstance($model)->getRow($select);
        return $row;
    }

    protected function _getMailComponent()
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId') . '_mail',
            array('ignoreVisible' => true)
        );
        return $component->getComponent();
    }
}
