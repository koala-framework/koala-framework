<?php
class Vpc_NewsletterCategory_Update_35004 extends Vps_Update
{
    private $_afterUpdateRequired = false;
    public function update()
    {
        $db = Vps_Registry::get('db');
        $db->query("ALTER TABLE  `vpc_newsletter_categories` ADD  `newsletter_component_id` VARCHAR( 200 ) NOT NULL AFTER  `id`");
        $db->query("ALTER TABLE  `vpc_newsletter_categories` ADD INDEX (  `newsletter_component_id` )");
        if ($db->query("SELECT COUNT(*) FROM `vpc_newsletter_categories`")->fetchColumn()) {
            $nlCId = $db->query("SELECT component_id FROM `vpc_newsletter` LIMIT 1")->fetchColumn();
            if ($nlCId) {
                $db->query("UPDATE `vpc_newsletter_categories` SET newsletter_component_id='$nlCId'");
            } else {
                $this->_afterUpdateRequired = true;
            }
        }
    }

    public function postUpdate()
    {
        if ($this->_afterUpdateRequired) {
            $c = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass('Vpc_NewsletterCategory_Component', array('limit'=>1));
            Vps_Registry::get('db')
                ->query("UPDATE `vpc_newsletter_categories` SET newsletter_component_id='$c->dbId'");
        }
    }

}
