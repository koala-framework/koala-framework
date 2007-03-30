E3.Component.Product = function(componentId, componentClass) {
    E3.Component.Product.superclass.constructor.call(this, componentId, componentClass);
    
};
YAHOO.lang.extend(E3.Component.Product, E3.Component.Abstract);


E3.Component.Product.Details = function(componentId, componentClass) {
    E3.Component.Product.Details.superclass.constructor.call(this, componentId, componentClass);
};
YAHOO.lang.extend(E3.Component.Product.Details, E3.Component.Abstract);

