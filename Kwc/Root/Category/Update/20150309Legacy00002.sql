ALTER TABLE  `kwf_pages` ADD  `device_visible` ENUM(  'all',  'hideOnMobile',  'onlyShowOnMobile' ) NOT NULL AFTER  `visible`;
