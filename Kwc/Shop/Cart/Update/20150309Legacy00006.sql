ALTER TABLE `kwc_shop_product_prices` DROP FOREIGN KEY `kwc_shop_product_prices_ibfk_1` ;
ALTER TABLE `kwc_shop_product_prices` ADD FOREIGN KEY ( `shop_product_id` ) 
    REFERENCES `kwc_shop_products` (
        `id`
    ) ON DELETE CASCADE ON UPDATE CASCADE ;
