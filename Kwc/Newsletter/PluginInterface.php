<?php
interface Kwc_Newsletter_PluginInterface
{
    public function getNewsletterStatisticRows($totalRecipients, &$pos);
    public function modifyRecipientsGridColumns(Kwf_Collection &$columns);
    public function modifyRecipientsSelect(Kwf_Model_Select &$select);
}
