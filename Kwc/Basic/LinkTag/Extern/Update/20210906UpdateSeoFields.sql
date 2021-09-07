ALTER TABLE `kwc_basic_link_extern` DROP `rel_nofollow`;
ALTER TABLE `kwc_basic_link_extern` DROP `rel_noopener`;
ALTER TABLE `kwc_basic_link_extern` DROP `rel_noreferrer`;
ALTER TABLE `kwc_basic_link_extern` ADD `rel_noindex` TINYINT NOT NULL ;
