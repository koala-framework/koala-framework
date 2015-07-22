var fields = {};
module.exports = {
    register: function(name, cls) {
        fields[name] = cls;
    },
    fields: fields
};
