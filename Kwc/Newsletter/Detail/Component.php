<?php
class Kwc_Newsletter_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    private $_toImport = array();

    /**
     * Cache for email addresses that should be checked against the rtr-ecg list
     * Key   = the same key as in $this->_toImport
     * Value = the email address that should be checked
     */
    private $_rtrCheck = array();

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['mail'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Newsletter_Detail_Mail_Component'
        );
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Newsletter/Detail/TabPanel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Newsletter/Detail/PreviewPanel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Newsletter/Detail/RecipientsPanel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Newsletter/Detail/RecipientsGridPanel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Newsletter/Detail/RecipientsQueuePanel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Newsletter/Detail/RecipientsAction.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Newsletter/Detail/Recipients.css';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Newsletter/Detail/StartNewsletterPanel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Newsletter/Detail/StartNewsletterPanel.scss';
        $ret['assetsAdmin']['files'][] = 'ext/src/widgets/StatusBar.js';
        $ret['assetsAdmin']['dep'][] = 'KwfFormDateTimeField';
        $ret['assetsAdmin']['dep'][] = 'KwfFormCards';
        $ret['componentName'] = 'Newsletter';
        $ret['checkRtrList'] = !!Kwf_Config::getValue('service.rtrlist.url');
        $ret['flags']['skipFulltext'] = true;

        $ret['extConfig'] = 'Kwc_Newsletter_Detail_ExtConfig';

        $ret['contentSender'] = 'Kwc_Newsletter_Detail_ContentSender';
        return $ret;
    }

    public function countQueue()
    {
        $model = $this->getData()->parent->getComponent()->getChildModel()->getDependentModel('Queue');
        $select = $model->select()->whereEquals('newsletter_id', $this->getData()->row->id);
        return $model->countRows($select);
    }

    public function removeFromQueue($model = '', $ids = array())
    {
        $ret = array();

        $newsletter = $this->getData()->row;
        $queueModel = $this->getData()->parent->getComponent()->getChildModel()->getDependentModel('Queue');
        $select = $queueModel->select()
            ->whereEquals('recipient_model', $model)
            ->whereEquals('recipient_id', $ids)
            ->whereEquals('newsletter_id', $newsletter->id);
        $queueModel->deleteRows($select);
    }

    public function importToQueue(Kwf_Model_Abstract $model, Kwf_Model_Select $select)
    {
        $ret = array('rtrExcluded' => array());

        // check if the necessary modelShortcut is set in 'mail' childComponent
        // this function checks if everything neccessary is set
        $this->getData()->getChildComponent('_mail')->getChildComponent('_redirect')
            ->getComponent()->getRecipientModelShortcut(get_class($model));

        if (!$model->hasColumnMappings('Kwc_Mail_Recipient_Mapping')) {
            throw new Kwf_Exception('Model "' . get_class($model) . '" has to implement column mapping "Kwc_Mail_Recipient_Mapping"');
        }

        if ($model->hasColumnMappings('Kwc_Mail_Recipient_UnsubscribableMapping')) {
            $unsubscribeColumn = $model->getColumnMapping(
                'Kwc_Mail_Recipient_UnsubscribableMapping', 'unsubscribed'
            );
            $select->whereEquals($unsubscribeColumn, 0);
        }
        if ($model->hasColumn('activated')) {
            $select->whereEquals('activated', 1);
        }
        $newsletter = $this->getData()->row;
        $mapping = $model->getColumnMappings('Kwc_Mail_Recipient_Mapping');
        $import = array();
        $emails = array();
        foreach ($model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select) as $e) {
            $import[] = array(
                'newsletter_id' => $newsletter->id,
                'recipient_model' => get_class($model),
                'recipient_id' => $e['id'],
                'searchtext' =>
                    $e[$mapping['firstname']] . ' ' .
                    $e[$mapping['lastname']] . ' ' .
                    $e[$mapping['email']]
            );
            $emails[] = $e[$mapping['email']];
        }

        // check against rtr-ecg list
        if (count($emails) && $this->_getSetting('checkRtrList')) {
            $badKeys = Kwf_Util_RtrList::getBadKeys($emails);

            // remove the bad rtr entries from the list
            if ($badKeys) {
                foreach ($badKeys as $badKey) {
                    $ret['rtrExcluded'][] = $emails[$badKey];
                    unset($import[$badKey]);
                }
            }
        }

        // add to model
        $queueModel = $this->getData()->parent->getComponent()->getChildModel()->getDependentModel('Queue');
        $queueModel->import(Kwf_Model_Db::FORMAT_ARRAY, $import, array('ignore' => true));
        return $ret;
    }
}
