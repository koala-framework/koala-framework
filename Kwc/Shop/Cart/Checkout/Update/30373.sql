ALTER TABLE vpc_shop_orders CHANGE origin origin ENUM( 'internet', 'phone', 'folder', 'fair' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'internet';
