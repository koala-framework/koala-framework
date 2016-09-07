var baseUrl = '';
module.exports = {
    set: function(v) {
        baseUrl = v;
    },
    get: function() {
        return baseUrl;
    }
};
