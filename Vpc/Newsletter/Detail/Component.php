<?php
class Vpc_Newsletter_Detail_Component extends Vpc_Directories_Item_Detail_Component
{
    private $_toImport = array();

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['mail'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Mail_Component'
        );
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Newsletter/Detail/MailingPanel.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Newsletter/Detail/MailPanel.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Newsletter/Detail/RecipientsPanel.js';
        $ret['assetsAdmin']['files'][] = 'ext/src/widgets/StatusBar.js';
        $ret['componentName'] = 'Newsletter';
        return $ret;
    }

    public function addToQueue(Vpc_Mail_Recipient_Interface $recipient)
    {
        $newsletter = $this->getData()->row;
        if ($newsletter->status == 'start') throw new Vps_ClientException('Cannot add recipients while sending a newsletter');
        $this->_toImport[] = array(
            'newsletter_id' => $newsletter->id,
            'recipient_model' => get_class($recipient->getModel()),
            'recipient_id' => $recipient->id,
            'status' => 'queued',
            'searchtext' =>
                $recipient->getMailTitle() . ' ' .
                $recipient->getMailFirstname() . ' ' .
                $recipient->getMailLastname() . ' ' .
                $recipient->getMailEmail()
        );
        return true;
    }

    public function saveQueue()
    {
        $ret = array();
        $newsletter = $this->getData()->row;
        $model = Vps_Model_Abstract::getInstance('Vpc_Newsletter_QueueModel');
        $select = $model->select()->whereEquals('newsletter_id', $newsletter->id);
        $ret['before'] = $model->countRows($select);
        $model->import(Vps_Model_Db::FORMAT_ARRAY, $this->_toImport, array('ignore' => true));
        $ret['after'] = $model->countRows($select);
        $ret['added'] = $ret['after'] - $ret['before'];
        return $ret;
    }
}
