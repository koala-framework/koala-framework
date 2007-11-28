Vps.Form.VpcLinkField = Ext.extend(Ext.form.TextField, 

    defaultAutoCreate : {tag: "input", type: "hidden"}

    pagesRendered: false

    setValue : function()
        Vps.Form.VpcLinkField.superclass.setValue.apply(this, arguments)

        if (!this.pagesRendered) 
            span = Ext.DomHelper.insertAfter(this.el, '<span></span>')
            this.pages = new Vps.Auto.TreePanel(
                controllerUrl: this.controllerUrl
                openedId: this.getValue()
                renderTo: span
                width: this.widt
            })

            this.pages.on('loaded', function() 
                this.pages.tree.on('click', function(node) 
                    this.setValue(node.id)
                }, this)
            }, this)
            this.pagesRendered = true
        
    }
   
    validateValue : function(value
    
        if (!value) 
            return false
        } else 
            return true
        
    
})

Ext.reg('vpclink', Vps.Form.VpcLinkField)
