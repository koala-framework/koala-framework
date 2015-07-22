var errorStyles = {};
module.exports = {
    register: function(name, cls) {
        errorStyles[name] = cls;
    },
    errorStyles: errorStyles
};
