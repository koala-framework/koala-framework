var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var Field = require('kwf/commonjs/frontend-form/field/field');
var kwfExtend = require('kwf/commonjs/extend');

var Static = kwfExtend(Field, {
    initField: function() {
    },
    getValue: function() {
        return null;
    },
    getFieldName: function() {
        return null; //TODO?
    },
    clearValue: function () {}
});

fieldRegistry.register('kwfFormFieldStatic', Static);
module.exports = Static;
