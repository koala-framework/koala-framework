<?php
class Kwc_Newsletter_QueueModel extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_newsletter_queue';
    protected $_rowClass = 'Kwc_Newsletter_QueueRow';
    protected $_referenceMap = array(
        'Newsletter' => array(
            'column' => 'newsletter_id',
            'refModelClass' => 'Kwc_Newsletter_Model'
        )
    );

    public function deleteRows($where)
    {
        $whereEquals = $where->getPart('whereEquals');
        if (!$whereEquals || !isset($whereEquals['newsletter_id'])) throw new Kwf_Exception('No newsletter_id set');
        $select = new Kwf_Model_Select();
        $select->whereEquals('id', $whereEquals['newsletter_id']);
        $newsletter = $this->getReferencedModel('Newsletter')->getRow($select);
        if (!$newsletter) throw new Kwf_Exception('No Newsletter found');
        if (in_array($newsletter->status, array('start', 'stop', 'finished', 'sending'))) {
            throw new Kwf_ClientException(trlKwf('Can only add users to a paused newsletter'));
        }

        parent::deleteRows($where);
    }
}
