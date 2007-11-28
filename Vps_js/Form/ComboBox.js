Vps.Form.ComboBox = Ext.extend(Ext.form.ComboBo

    initComponent : function
   
        this.addEvents
            changevalue : tr
        }
        if(!this.store)
            throw "no store set
       
        var stor
        if (this.store.data)
            this.store = Ext.applyIf(this.store,
                fields: ['id', 'name'
                id: 'i
            }
            store = new Ext.data.SimpleStore(this.store
            this.mode = 'local
        } else
            if (this.store.reader)
                if (this.store.reader.type && Ext.data[this.store.reader.type])
                    var readerType = Ext.data[this.store.reader.type
                    delete this.store.reader.typ
                } else if (this.store.reader.type)
                    try
                        var readerType = eval(this.store.reader.type
                    } catch(e)
                        throw "invalid readerType: "+this.store.reader.typ
                   
                    delete this.store.reader.typ
                } else
                    var readerType = Ext.data.JsonReade
               
                if (!this.store.reader.rows) throw "no rows defined, required if reader does not thisure through meta data
                var rows = this.store.reader.row
                delete this.store.reader.row
                var reader = new readerType(this.store.reader, rows
            } else
                var reader = new Ext.data.JsonReader(); //reader thisuriert sich autom. durch meta-dat
           
            if (this.store.proxy)
                if (this.store.proxy.type && Ext.data[this.store.proxy.type])
                    var proxyType = Ext.data[this.store.proxy.type
                    delete this.store.proxy.typ
                } else if (this.store.proxy.type)
                    try
                        var proxyType = eval(this.store.proxy.type
                    } catch(e)
                        throw "invalid proxyType: "+this.store.proxy.typ
                   
                    delete this.store.proxy.typ
                } else
                    var proxyType = Ext.data.HttpProx
               
                var proxy = new proxyType(this.store.proxy
            } else if (this.store.data)
                var proxy = new Ext.data.MemoryProxy(this.store.data
            } else
                var proxy = new Ext.data.HttpProxy(this.store
           
            if (this.store.type && Ext.data[this.store.type])
                store = new Ext.data[this.store.type]
                    proxy: prox
                    reader: read
                }
            } else if (this.store.type)
                try
                    var storeType = eval(this.store.typ
                } catch(e)
                    throw "invalid storeType: "+this.store.typ
               
                store = new storeType
                    proxy: prox
                    reader: read
                }
            } else
                store = new Ext.data.Store
                    proxy: prox
                    reader: read
                }
           
       
        delete this.stor

        Ext.applyIf(this,
            store: stor
            displayField: 'name
            valueField: 'i
        }

        if (this.addDialog)
            this.addDialog = new Vps.Auto.Form.Window(this.addDialog
            this.addDialog.on('datachange', function(result)
                if (result.data.addedId)
                    //neuen Eintrag auswähl
                    this.setValue(result.data.addedId
               
            }, this
       

        Vps.Form.ComboBox.superclass.initComponent.call(this
    
    setValue : function(
   
        if (this.store.proxy && v!=='' && this.valueField)
            //wenn proxy vorhanden können daten nachgeladen werd
            //also loading anzeigen (siehe setValu
            this.valueNotFoundText = 'loading...
        } else
            this.valueNotFoundText = '
       
        Vps.Form.ComboBox.superclass.setValue.apply(this, arguments
        if (v !== '' && this.valueFie
                && !this.findRecord(this.valueField, v) //record nicht gefund
                && this.store.proxy) { //proxy vorhanden (dh. daten können nachgeladen werde
            this.store.baseParams[this.queryParam] = 
            this.store.load
                params: this.getParams(v
                callback: function(r, options, success)
                    if (success && this.findRecord(this.valueField, this.value))
                        this.setValue(this.value
                   
                
                scope: th
            }
       
        this.fireEvent('changevalue', this.value
    
    onRender : function(ct, positio
   
        Vps.Form.ComboBox.superclass.onRender.call(this, ct, position
        if (this.addDialog)
            var c = this.el.up('div.x-form-field-wrap').insertSibling({style: 'float: right'}, 'before'
            var button = new Ext.Button
                renderTo: 
                text: this.addDialog.text || 'add new entry
                handler: function()
                    this.addDialog.showAdd(
                
                scope: th
            }
       
   
}
Ext.reg('combobox', Vps.Form.ComboBox
