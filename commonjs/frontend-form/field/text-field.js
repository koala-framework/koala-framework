var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');

var TextField = kwfExtend(Field, {
});

fieldRegistry.register('kwfFormFieldTextField', TextField);
module.exports = TextField;
