<?php
class Kwc_Newsletter_Detail_PreviewController extends Kwc_Mail_PreviewController
{
    protected function _getRecipient()
    {
        $rs = $this->_getMailComponent()->getRecipientSources();
        $recipientId = $this->_getParam('recipientId');
        if (!$recipientId) {
            $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
                $this->_getParam('componentId'),
                array('ignoreVisible' => true)
            );
            $source = reset($rs);
            $model = Kwf_Model_Abstract::getInstance($source['model']);
            $select = $model->select();
            if ($model->hasColumn('newsletter_component_id')) {
                $select->whereEquals('newsletter_component_id', $component->parent->componentId);
            }
            if (isset($source['select'])) $select->merge($source['select']);
            $row = $model->getRow($select);
            if (!$row) throw new Kwf_Exception_Client(trlKwf('Preview cannot be shown because it needs at least one recipient of this newsletter'));
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
