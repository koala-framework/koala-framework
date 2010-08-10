ALTER TABLE `vps_enquiries` ADD `sent_mail_content_text` TEXT NOT NULL ;
ALTER TABLE `vps_enquiries` ADD `sent_mail_content_html` TEXT NOT NULL ;
ALTER TABLE `vps_enquiries` ADD `mail_attachments` TEXT NOT NULL AFTER `serialized_mail_essentials` ;
