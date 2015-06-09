Kwf.FrontendForm.Cards = Ext2.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        var config = this.form.getFieldConfig(this.getFieldName());
        var combobox = this.form.findField(config.combobox);
        combobox.el.select('input.submit').remove(); //remove non-js fallback
        combobox.on('change', function(value) {
            this.el.select('.kwfFormContainerCard .kwfFormCard').addClass('inactive');
            this.el.select('.kwfFormContainerCard.'+value+' .kwfFormCard').removeClass('inactive');
        }, this);
    },
    getFieldName: function() {
        var classNames = this.el.dom.className.split(' ');
        return classNames[classNames.length-1];
    },
    getValue: function() {
        return null;
    },
    clearValue: function() {
    },
    setValue: function(value) {
    }
});

Kwf.FrontendForm.fields['kwfFormContainerCards'] = Kwf.FrontendForm.Cards;
