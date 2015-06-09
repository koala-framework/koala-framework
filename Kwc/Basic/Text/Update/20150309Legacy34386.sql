ALTER TABLE  `kwc_basic_text` ADD  `uses_styles` TINYINT NOT NULL COMMENT  'true if content contains class="style (customs style)' AFTER  `data` ,
    ADD INDEX (  `uses_styles` );

UPDATE `kwc_basic_text` SET uses_styles = content LIKE '%class="style%';
