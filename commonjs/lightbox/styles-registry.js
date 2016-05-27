var styles = {};
module.exports = {
    register: function(name, cls) {
        styles[name] = cls;
    },
    styles: styles
};
