ALTER TABLE `vpc_shop_product_prices` DROP FOREIGN KEY `vpc_shop_product_prices_ibfk_1` ;
ALTER TABLE `vpc_shop_product_prices` ADD FOREIGN KEY ( `shop_product_id` ) 
    REFERENCES `vpc_shop_products` (
        `id`
    ) ON DELETE CASCADE ON UPDATE CASCADE ;
