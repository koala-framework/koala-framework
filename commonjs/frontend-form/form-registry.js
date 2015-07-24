var formsByComponentId = {};
module.exports = {
    formsByComponentId: formsByComponentId,
    getFormByComponentId: function(id) {
        return formsByComponentId[id];
    }
};
