<?php
class Kwc_Newsletter_Detail_PreviewController extends Kwc_Mail_PreviewController
{
    protected function _getRecipient()
    {
        $rs = $this->_getMailComponent()->getRecipientSources();
        $recipientId = $this->_getParam('recipientId');
        if (!$recipientId) {
            $component = Kwf_Component_Data_Root::getInstance()->getComponentById(
                $this->_getParam('componentId'),
                array('ignoreVisible' => true)
            );
            $select = new Kwf_Model_Select();
            $select->whereEquals('newsletter_component_id', $component->parent->componentId);
            $source = reset($rs);
            if (isset($source['select'])) $select->merge($source['select']);
            $model = Kwf_Model_Abstract::getInstance($source['model']);
            $row = $model->getRow($select);
            if (!$row) throw new Kwf_Exception_Client(trlKwf('Preview only works with recipients in the newsletter'));
            $recipientId = $row->id;
        }
        $select = new Kwf_Model_Select();
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
