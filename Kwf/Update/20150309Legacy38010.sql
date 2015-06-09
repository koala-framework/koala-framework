#tags: kwc
ALTER TABLE `cache_component` CHANGE `renderer` `renderer` ENUM('component','mail_html','mail_txt','export_html') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
