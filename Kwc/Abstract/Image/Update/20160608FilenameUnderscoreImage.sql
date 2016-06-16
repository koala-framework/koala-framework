UPDATE `kwc_basic_image` SET filename = REPLACE(filename, '_', '-') WHERE filename LIKE '%\_%';
