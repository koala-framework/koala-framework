var $ = require('jQuery');

module.exports = function(el) {
    var formEl = $(el).find('.kwfUp-kwcForm > form');
    if (formEl) {
        formEl = formEl.closest('.kwcForm');
        return formEl.data('kwcForm');
    }
    return null;
};
