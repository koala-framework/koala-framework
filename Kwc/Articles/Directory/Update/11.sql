ALTER TABLE  `kwc_article_tag_suggestions` CHANGE  `status`  `status` ENUM(  'new',  'accepted',  'deneyed',  'denied' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

UPDATE `kwc_article_tag_suggestions` SET `status` = 'denied' WHERE `status` = 'deneyed';

ALTER TABLE  `kwc_article_tag_suggestions` CHANGE  `status`  `status` ENUM(  'new',  'accepted',  'denied' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;