// @require ModernizrNetworkXhr2
// @require KwfLoading

var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var fieldRegistry = require('kwf/frontend-form/field-registry');
var errorStyleRegistry = require('kwf/frontend-form/error-style-registry');
var formRegistry = require('kwf/frontend-form/form-registry');
var statistics = require('kwf/statistics');
var dataLayer = require('kwf/data-layer');
var t = require('kwf/trl');

require('kwf/frontend-form/error-style/above');
require('kwf/frontend-form/error-style/below-field');
require('kwf/frontend-form/error-style/bubble');
require('kwf/frontend-form/error-style/icon-bubble');

require('kwf/frontend-form/field/field');
require('kwf/frontend-form/field/cards');
require('kwf/frontend-form/field/checkbox');
require('kwf/frontend-form/field/date-select');
require('kwf/frontend-form/field/field-set');
require('kwf/frontend-form/field/file');
require('kwf/frontend-form/field/multi-checkbox');
require('kwf/frontend-form/field/radio');
require('kwf/frontend-form/field/select');
require('kwf/frontend-form/field/static');
require('kwf/frontend-form/field/recaptcha');
require('kwf/frontend-form/field/text-area');
require('kwf/frontend-form/field/text-field');

var FormComponent = function(form)
{
    form.get(0).kwcForm = this;
    form.data('kwcForm', this);

    this.el = form.find('.kwfUp-formContainer');
    if (!this.el.length) return;

    var config = form.data('config');
    if (!config) return;
    this.config = config;
    this._submitDisabled = 0;

    formRegistry.formsByComponentId[this.config.componentId] = this;


    if (this.el.find('form').get(0).enctype == 'multipart/form-data' && this.config.useAjaxRequest) {
        if (Modernizr.xhr2) {
            this.el.find('form').get(0).enctype = 'application/x-www-form-urlencoded';
        }
        this.config.useAjaxRequest = Modernizr.xhr2;
    }

    this.fields = [];
    var fieldEls = form.find('.kwfUp-kwfField');
    for (var fieldElIndex=0; fieldElIndex<fieldEls.length; fieldElIndex++) {
        var fieldEl = fieldEls[fieldElIndex];
        var classes = fieldEl.className.split(' ');
        var fieldConstructor = false;
        $.each(classes, function (indx, c) {
            c = c.replace('kwfUp-', '');
            if (fieldRegistry.fields[c]) {
                fieldConstructor = fieldRegistry.fields[c];
            }
        });
        if (fieldConstructor) {
            var field = new fieldConstructor($(fieldEl), this);
            this.fields.push(field);
        }
    }

    this.errorStyle = new errorStyleRegistry.errorStyles[this.config.errorStyle](this);

    $.each(this.fields, function(i, f) {
        f.initField();
    });

    if (this.config.hideForValue) {
        $.each(this.config.hideForValue, (function(indx, hideForValue) {
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
        }).bind(this));
    }

    var button = form.find('form button.kwfUp-submit');
    if (button) {
        button.on('click', this.onSubmit.bind(this));
    }

    $.each(this.fields, (function(i, f) {
        f.on('change', function() {
            this.el.trigger('kwfUp-form-fieldChange', f);
        }, this);
    }).bind(this));
};
FormComponent.prototype = {

    on: function(event, cb, scope)
    {
        if (typeof scope != 'undefined') cb = cb.bind(scope);
        this.el.on('kwfUp-form-'+event, cb);
    },

    getFieldConfig: function(fieldName)
    {
        if (this.config.fieldConfig[fieldName]) {
            return this.config.fieldConfig[fieldName];
        }
        return {};
    },
    findField: function(fieldName) {
        var ret = null;
        $.each(this.fields, function(i, f) {
            if (f.getFieldName() == fieldName) {
                ret = f;
                return true;
            }
        });
        return ret;
    },
    getValues: function() {
        var ret = {};
        $.each(this.fields, function(i, f) {
            ret[f.getFieldName()] = f.getValue();
        });
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
        $.each(this.fields, $.proxy(function(i, f) {
            f.clearValue();
        }, this));
    },
    setValues: function(values) {
        for (var i in values) {
            var f = this.findField(i);
            if (f) f.setValue(values[i]);
        }
    },
    disableSubmit: function() {
        this._submitDisabled++;
        this.el.find('.kwfUp-submitWrapper .kwfUp-button button').get(0).disabled = true;
        this.el.find('.kwfUp-submitWrapper .kwfUp-button').addClass('kwfUp-disabled');
    },
    enableSubmit: function() {
        this._submitDisabled--;

        if (this._submitDisabled === 0) {
            this.el.find('.kwfUp-submitWrapper .kwfUp-button button').get(0).disabled = false;
            this.el.find('.kwfUp-submitWrapper .kwfUp-button').removeClass('kwfUp-disabled');
        }
    },
    isSubmitDisabled: function() {
        return this._submitDisabled > 0;
    },
    onSubmit: function(e)
    {
        if (this.isSubmitDisabled()) {
            e.preventDefault();
            return;
        }

        //return false to cancel submit
        if (this.el.triggerHandler('kwfUp-form-beforeSubmit', this, e) === false) {
            e.preventDefault();
            return;
        }

        if (!this.config.useAjaxRequest || this.ajaxRequestSubmitted) return;
        this.submit();
        e.preventDefault();
    },

    submit: function()
    {
        var button = this.el.find('.kwfUp-submitWrapper .kwfUp-button');
        button.prepend('<div class="kwfUp-saving"></div>');
        button.find('.kwfUp-submit').css('visibility', 'hidden');

        this.errorStyle.hideErrors();

        var data = this.el.find('form').serialize();
        data += '&'+$.param(this.config.baseParams);
        data += '&'+this.el.find('.kwfUp-submitWrapper .kwfUp-button button').prop('name')+'=1';
        $.ajax({
            url: this.config.controllerUrl + '/json-save',
            type: 'POST',
            ignoreErrors: true,
            data: data,
            dataType: 'json',
            error: (function() {
                //on failure try a plain old post of the form
                this.ajaxRequestSubmitted = true; //avoid endless recursion
                button.find('.kwfUp-submit').get(0).click();
            }).bind(this),
            success: (function(r) {

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
                    button.find('.kwfUp-saving').remove();
                    button.find('.kwfUp-submit').css('visibility', 'visible');
                }

                // show success content
                if (r.successContent) {
                    this.el.trigger('kwfUp-form-submitSuccessNoError', this, r);
                    statistics.trackEvent(t.trlKwf('Form Submission'), location.pathname, button.find('span').text());
                    var dataLayerEntry = this.config.submitDataLayer ? this.config.submitDataLayer : {};
                    if (!dataLayerEntry.event) {
                        dataLayerEntry.event = "form-submit";
                    }
                    dataLayer.push(dataLayerEntry);
                    var el = this.el.parent().append(r.successContent);
                    if (this.config.hideFormOnSuccess) {
                        this.el.hide();
                    } else {
                        (function(el) {
                            el.remove();
                            onReady.callOnContentReady(this.el, {newRender: false});
                        }).defer(5000, this, [el]);
                    }
                    onReady.callOnContentReady(el, {newRender: true});
                } else if (r.successUrl) {
                    this.el.trigger('kwfUp-form-submitSuccessNoError', this, r);
                    statistics.trackEvent(t.trlKwf('Form Submission'), location.pathname, button.find('span').text());
                    var dataLayerEntry = this.config.submitDataLayer ? this.config.submitDataLayer : {};
                    if (!dataLayerEntry.event) {
                        dataLayerEntry.event = "form-submit";
                    }
                    dataLayer.push(dataLayerEntry);
                    document.location.href = r.successUrl;
                } else {
                    //errors are shown, lightbox etc needs to resize
                    onReady.callOnContentReady(this.el, {newRender: false});
                }

                var scrollTo = null;
                if (!hasErrors) {
                    // Scroll to top of form
                    scrollTo = this.el.offset().top;
                } else {
                    // Scroll to first error. If there are form-errors those are first
                    if (!r.errorMessages.length) { // there are no form-errors
                        // Get position of first error field
                        for (var fieldName in r.errorFields) {
                            var field = this.findField(fieldName);
                            if (field) {
                                var pos = field.el.offset().top;
                                if (scrollTo == null || scrollTo > pos) {
                                    scrollTo = pos;
                                }
                            }
                        }
                    }
                    if (scrollTo == null) { // no field errors found or only form errors
                        // form-errors are shown at the top of the form
                        scrollTo = this.el.offset().top;
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

                this.el.trigger('kwfUp-form-submitSuccess', this, r);
            }).bind(this)
        });
    }
};

module.exports = FormComponent;
