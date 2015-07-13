module.exports = function(el) {
    var formEl = el.find('.kwfup-kwcForm > form');
    if (formEl) {
        formEl = formEl.closest('.kwcForm');
        return formEl.data('kwcForm');
    }
    return null;
};
