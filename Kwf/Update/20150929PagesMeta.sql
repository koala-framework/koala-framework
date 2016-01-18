#tags: kwc
ALTER TABLE  `kwf_pages_meta` ADD  `sitemap_changefreq` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER  `meta_noindex`;
ALTER TABLE  `kwf_pages_meta` ADD  `sitemap_priority` DECIMAL(2, 1) NOT NULL AFTER  `meta_noindex`;
UPDATE kwf_pages_meta SET sitemap_changefreq = 'weekly';
UPDATE kwf_pages_meta SET sitemap_priority = 0.5;
