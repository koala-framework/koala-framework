<?php
class Kwc_NewsletterCategory_Update_20150309Legacy35004 extends Kwf_Update
{
    private $_afterUpdateRequired = false;
    public function update()
    {
        $db = Kwf_Registry::get('db');
        $db->query("ALTER TABLE  `kwc_newsletter_categories` ADD  `newsletter_component_id` VARCHAR( 200 ) NOT NULL AFTER  `id`");
        $db->query("ALTER TABLE  `kwc_newsletter_categories` ADD INDEX (  `newsletter_component_id` )");
        if ($db->query("SELECT COUNT(*) FROM `kwc_newsletter_categories`")->fetchColumn()) {
            $nlCId = $db->query("SELECT component_id FROM `kwc_newsletter` LIMIT 1")->fetchColumn();
            if ($nlCId) {
                $db->query("UPDATE `kwc_newsletter_categories` SET newsletter_component_id='$nlCId'");
            } else {
                $this->_afterUpdateRequired = true;
            }
        }
    }

    public function postUpdate()
    {
        if ($this->_afterUpdateRequired) {
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentByClass('Kwc_NewsletterCategory_Component', array('limit'=>1));
            Kwf_Registry::get('db')
                ->query("UPDATE `kwc_newsletter_categories` SET newsletter_component_id='$c->dbId'");
        }
    }

}
