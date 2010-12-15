ALTER TABLE `vpc_shop_order_products` ADD `add_component_class` VARCHAR( 200 ) NOT NULL ;
UPDATE vpc_shop_order_products SET add_component_class='Vpc_Babytuch_Shop_AddToCart_Component';

