ALTER TABLE `kwc_shop_order_products` ADD `add_component_class` VARCHAR( 200 ) NOT NULL ;
UPDATE kwc_shop_order_products SET add_component_class='Kwc_Babytuch_Shop_AddToCart_Component';

