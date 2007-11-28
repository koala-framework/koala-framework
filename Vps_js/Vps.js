Ext.BLANK_IMAGE_URL = '/assets/ext/resources/images/default/s.gif

Ext.namespac
'Vps', 'Vpc
'Vps.Component
'Vps.User.Login
'Vps.Auto
'Vps.For


Ext.applyIf(Array.prototype,

    //to use array.each direct
    each : function(fn, scope
        Ext.each(this, fn, scope
    

    //add is alias for pu
    add : function()
        this.push.apply(this, arguments
   
}

Ext.onReady(function

//     Ext.state.Manager.setProvider(new Ext.state.CookieProvider()

//     Ext.form.Field.prototype.msgTarget = 'side';// turn on validation errors beside the field global

    if (Ext.QuickTips)
        //init quicktips when load
        Ext.QuickTips.init(
   
}

Vps.application = { version: '{$application.version}' 

Vps.handleError = function(

    if (e.toString) e = e.toString(
    if (e.message) e = e.messag
    if(Ext.get('loading'))
        Ext.get('loading').fadeOut({remove: true}
   
    if (Vps.debug)
        throw e; //re-thr
    } else
        Ext.Msg.alert('Error', "Ein Fehler ist aufgetreten."
        Ext.Ajax.request
            url: '/error/jsonMail
            params: {msg: 
        }
   

