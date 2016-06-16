UPDATE `kwc_basic_downloadtag` SET filename = REPLACE(filename, '_', '-') WHERE filename LIKE '%\_%';
