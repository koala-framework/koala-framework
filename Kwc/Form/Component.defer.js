// @require ModernizrNetworkXhr2

Kwf.Utils.ResponsiveEl('.cssClass.default', [{maxWidth: 500, cls: 'veryNarrow'}, {minWidth: 500, cls: 'gt500'}, {minWidth: 350, cls: 'gt350'}]);
Kwf.Utils.ResponsiveEl('.cssClass.centerDefault', [{maxWidth: 500, cls: 'veryNarrow'}, {minWidth: 500, cls: 'gt500'}, {minWidth: 350, cls: 'gt350'}]);
Kwf.Utils.ResponsiveEl('.cssClass.smallBox', [{maxWidth: 500, cls: 'veryNarrow'}, {minWidth: 350, cls: 'gt350'}]);
Kwf.Utils.ResponsiveEl('.cssClass.center', [{maxWidth: 500, cls: 'veryNarrow'}, {minWidth: 350, cls: 'gt350'}]);

Ext2.ns('Kwc.Form');
Kwc.Form.findForm = function(el) {
    var formEl = el.child('.kwcForm > form');
    if (formEl) {
        formEl = formEl.parent('.kwcForm');
        return formEl.kwcForm;
    }
    return null;
};
Kwc.Form.formsByComponentId = {};

Kwc.Form.Component = function(form)
{
    this.addEvents('beforeSubmit', 'submitSuccess', 'fieldChange');
    this.el = form;
    var config = form.parent().down('.config', true);
    if (!config) return;
    config = Ext2.decode(config.value);
    if (!config) return;
    this.config = config;
    this._submitDisabled = 0;

    Kwc.Form.formsByComponentId[this.config.componentId] = this;


    if (this.el.down('form').dom.enctype == 'multipart/form-data' && this.config.useAjaxRequest) {
        if (Modernizr.xhr2) {
            this.el.down('form').dom.enctype = 'application/x-www-form-urlencoded';
        }
        this.config.useAjaxRequest = Modernizr.xhr2;
    }

    this.fields = [];
    var fieldEls = $(form.dom).find('.kwfField');
    for (var fieldElIndex=0; fieldElIndex<fieldEls.length; fieldElIndex++) {
        var fieldEl = Ext2.get(fieldEls[fieldElIndex]);
        var classes = fieldEl.dom.className.split(' ');
        var fieldConstructor = false;
        classes.each(function (c) {
            if (Kwf.FrontendForm.fields[c]) {
                fieldConstructor = Kwf.FrontendForm.fields[c];
            }
        }, this);
        if (fieldConstructor) {
            var field = new fieldConstructor(fieldEl, this);
            this.fields.push(field);
        }
    }

    this.errorStyle = new Kwf.FrontendForm.errorStyles[this.config.errorStyle](this);

    this.fields.forEach(function(f) {
        f.initField();
    });

    if (this.config.hideForValue) {
        this.config.hideForValue.each(function(hideForValue) {
            var field = this.findField(hideForValue.field);
            field.on('change', function(v, f) {
                if (v == hideForValue.value) {
                    this.findField(hideForValue.hide).hide();
                } else {
                    this.findField(hideForValue.hide).show();
                }
            }, this);

            //initialize on startup
            if (field.getValue() == hideForValue.value) {
                this.findField(hideForValue.hide).hide();
            }
        }, this);
    }

    var button = form.child('form button.submit');
    if (button) {
        button.on('click', this.onSubmit, this);
    }

    this.fields.forEach(function(f) {
        f.on('change', function() {
            this.fireEvent('fieldChange', f);
        }, this);
    }, this);
};
Ext2.extend(Kwc.Form.Component, Ext2.util.Observable, {
    getFieldConfig: function(fieldName)
    {
        if (this.config.fieldConfig[fieldName]) {
            return this.config.fieldConfig[fieldName];
        }
        return {};
    },
    findField: function(fieldName) {
        var ret = null;
        this.fields.each(function(f) {
            if (f.getFieldName() == fieldName) {
                ret = f;
                return true;
            }
        }, this);
        return ret;
    },
    getValues: function() {
        var ret = {};
        this.fields.each(function(f) {
            ret[f.getFieldName()] = f.getValue();
        }, this);
        return ret;
    },
    //returns values including parameters required for processInput to be considered as posted
    getValuesIncludingPost: function() {
        var ret = this.getValues();
        ret[this.config.componentId] = true;
        ret[this.config.componentId+'-post'] = true;
        return ret;
    },
    clearValues: function() {
        this.fields.each(function(f) {
            f.clearValue();
        }, this);
    },
    setValues: function(values) {
        for (var i in values) {
            var f = this.findField(i);
            if (f) f.setValue(values[i]);
        }
    },
    disableSubmit: function() {
        this._submitDisabled++;
        this.el.child('.submitWrapper .button button').dom.disabled = true;
        this.el.child('.submitWrapper .button').addClass('disabled');
    },
    enableSubmit: function() {
        this._submitDisabled--;

        if (this._submitDisabled === 0) {
            this.el.child('.submitWrapper .button button').dom.disabled = false;
            this.el.child('.submitWrapper .button').removeClass('disabled');
        }
    },
    isSubmitDisabled: function() {
        return this._submitDisabled > 0;
    },
    onSubmit: function(e)
    {
        if (this.isSubmitDisabled()) {
            e.stopEvent();
            return;
        }

        //return false to cancel submit
        if (this.fireEvent('beforeSubmit', this, e) === false) {
            e.stopEvent();
            return;
        }

        if (!this.config.useAjaxRequest || this.ajaxRequestSubmitted) return;
        this.submit();
        e.stopEvent();
    },
    
    submit: function()
    {
        var button = this.el.child('.submitWrapper .button');
        button.down('.saving').show();
        button.down('.submit').hide();

        this.errorStyle.hideErrors();

        Ext2.Ajax.request({
            url: this.config.controllerUrl + '/json-save',
            ignoreErrors: true,
            params: this.config.baseParams,
            form: this.el.down('form'),
            failure: function() {
                //on failure try a plain old post of the form
                this.ajaxRequestSubmitted = true; //avoid endless recursion
                button.down('.submit').dom.click();
            },
            success: function(response, options, r) {

                this.errorStyle.showErrors({
                    errorFields: r.errorFields,
                    errorMessages: r.errorMessages,
                    errorPlaceholder: r.errorPlaceholder
                });

                for (var fieldName in r.errorFields) {
                    var field = this.findField(fieldName);
                    field.onError(r.errorFields[fieldName]);
                }

                var hasErrors = false;
                if (r.errorMessages && r.errorMessages.length) {
                    hasErrors = true;
                }
                for (var fieldName in r.errorFields) {
                    hasErrors = true;
                }

                if (!r.successUrl) {
                    button.down('.saving').hide();
                    button.down('.submit').show();
                }

                // show success content
                if (r.successContent) {
                    var el = this.el.parent().createChild({
                        html: r.successContent
                    });
                    if (this.config.hideFormOnSuccess) {
                        this.el.enableDisplayMode('block');
                        this.el.hide();
                    } else {
                        (function(el) {
                            el.remove();
                            Kwf.callOnContentReady(this.el.dom, {newRender: false});
                        }).defer(5000, this, [el]);
                    }
                    Kwf.callOnContentReady(el.dom, {newRender: true});
                } else if (r.successUrl) {
                    document.location.href = r.successUrl;
                } else {
                    //errors are shown, lightbox etc needs to resize
                    Kwf.callOnContentReady(this.el.dom, {newRender: false});
                }

                var scrollTo = null;
                if (!hasErrors) {
                    // Scroll to top of form
                    scrollTo = this.el.getY();
                } else {
                    // Scroll to first error. If there are form-errors those are first
                    if (!r.errorMessages.length) { // there are no form-errors
                        // Get position of first error field
                        for (var fieldName in r.errorFields) {
                            var field = this.findField(fieldName);
                            if (field) {
                                var pos = field.el.getY();
                                if (scrollTo == null || scrollTo > pos) {
                                    scrollTo = pos;
                                }
                            }
                        }
                    }
                    if (scrollTo == null) { // no field errors found or only form errors
                        // form-errors are shown at the top of the form
                        scrollTo = this.el.getY();
                    }
                }
                if (scrollTo != null) {
                    //if scrollto is already on screen
                    var height = $(window).height();
                    var scrollPosY = $(window).scrollTop();
                    if (scrollTo < scrollPosY || scrollTo > scrollPosY + height) {
                        scrollTo -= 20;
                        var stopAnimationFunction = function(e) {
                            if ( e.which > 0 || e.type == "mousedown"
                                || e.type == "mousewheel"
                                || e.type == 'touchstart'){
                                $("html, body").stop();
                                $('body,html')
                                    .unbind('scroll mousedown DOMMouseScroll mousewheel keyup touchstart',
                                    stopAnimationFunction);
                            }
                        };
                        $('html, body').animate({
                            scrollTop: scrollTo
                        }, 2000, 'swing', function () {
                            $('body,html')
                                .unbind('scroll mousedown DOMMouseScroll mousewheel keyup touchstart',
                                stopAnimationFunction);
                        });
                        //This is a fix for the problem that it's not possible to
                        //scroll while the animation is running. This stops the
                        //animation on scroll, click or key-down
                        //http://stackoverflow.com/questions/2834667/how-can-i-differentiate-a-manual-scroll-via-mousewheel-scrollbar-from-a-javasc?answertab=oldest#tab-top
                        $('body,html').bind('scroll mousedown DOMMouseScroll mousewheel keyup touchstart', stopAnimationFunction);
                    }
                }

                this.fireEvent('submitSuccess', this, r);
            },
            scope: this
        });
    }
});

Kwf.onElementReady('.kwcForm > form', function form(form) {
    form = form.parent('.kwcForm', false);
    if (!form.dom.kwcForm) {
        form.dom.kwcForm = new Kwc.Form.Component(form);
        form.kwcForm = form.dom.kwcForm;
    }
}, { priority: -10, defer: true }); //initialize form very early, as many other components access it

