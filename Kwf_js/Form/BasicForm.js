Ext.form.BasicForm.override({
    resetDirty: function() {
        this.items.each(function(field) {
            field.resetDirty();
        });
    },
    setDefaultValues: function() {
        this.items.each(function(field) {
            field.setDefaultValue();
        }, this);
    },
    clearValues: function() {
        this.items.each(function(field) {
            if (field.rendered) field.clearValue();
        }, this);
    },

    //override stupid Ext behavior
    //better to ask the individual form fields
    //needed for: Checkbox, ComboBox, SwfUpload, Date...
    getValues: function() {
        var ret = {};
        this.items.each(function(field) {
            if (field.getName && field.getName()) {
                ret[field.getName()] = field.getValue();
            }
        }, this);
        return ret;
    }
});

Ext.apply(Ext.form.VTypes, {
    //E-Mail Validierung darf ab Ext 2.2 keine Bindestriche mehr haben, jetzt schon wieder
    email:  function(v) {
        return /^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/.test(v);
    },
    emailMask : /[a-z0-9_\.\-@+]/i, //include +

    urltel: function(v) {
        return /^(tel:\/\/[\d\s]+|(((https?)|(ftp)):\/\/([\-\w]+\.)+\w{2,3}(\/[%\-\w]+(\.\w{2,})?)*(([\w\-\.\?\\\/+@&#;`~=%!]*)(\.\w{2,})?)*\/?))+$/.test(v);
    },
    urltelTest: trlKwf('This field should be a URL in the format "http://www.domain.com" or tel://0043 1234'),

    //Ersetzt alles außer a-z, 0-9 - durch _. So wie Kwf_Filter_Ascii
    //standard-ext implementierung überschrieben um den - zu erlauben
    alphanum:  function(v) {
        return /^[a-zA-Z0-9_\-]+$/.test(v);
    },
    alphanumText : trlKwf('This field should only contain letters, numbers, - and _'),
    alphanumMask : /[a-z0-9_\-]/i,

    num:  function(v) {
        return /^[0-9]+$/.test(v);
    },
    numText : trlKwf('This field should only contain numbers'),
    numMask : /[0-9]/,

    time: function(val, field) {
        return /^([0-9]{2}):([0-9]{2}):([0-9]{2})$/i.test(val);
    },
    timeText: trlKwf('Not a valid time.  Must be in the format "12:34:00".'),
    timeMask: /[\d:]/i
});
