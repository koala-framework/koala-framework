ALTER TABLE  `kwc_paragraphs` ADD  `device_visible` ENUM(  'all',  'hideOnMobile',  'onlyShowOnMobile' ) NOT NULL AFTER  `visible`;
