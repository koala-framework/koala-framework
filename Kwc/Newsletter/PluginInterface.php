<?php
interface Kwc_Newsletter_PluginInterface
{
    const RECIPIENTS_GRID_TYPE_ADD_TO_QUEUE = 'recipientsGridAddToQueue';
    const RECIPIENTS_GRID_TYPE_EDIT_SUBSCRIBERS = 'recipientsGridEditSubscribers';

    public function getNewsletterStatisticRows($totalRecipients);
    public function modifyRecipientsGridColumns(Kwf_Collection $columns, $gridType);
    public function modifyRecipientsSelect(Kwf_Model_Select $select, $gridType);
}
