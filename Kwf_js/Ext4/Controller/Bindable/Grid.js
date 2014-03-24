Ext4.define('Kwf.Ext4.Controller.Bindable.Grid', {
    extend: 'Kwf.Ext4.Controller.Bindable.Abstract',

    relation: null,
    gridController: null,
    reloadRowOnSave: false,

    init: function()
    {
        if (this.reloadRowOnSave) {
            //savesuccess is fired by gridController on sync after delete
            this.gridController.on('savesuccess', this._reloadLoadedRow, this);
        }
    },

    load: function(row)
    {
        var storeName = this.relation+'Store'; //same naming as in Ext.data.association.HasMany
        if (this._loadedRecord && this._loadedRecord[storeName]) {
            //if new row has same id as currently loaded copy the store
            //this makes sures dirty values are kept
            if (row.getId() == this._loadedRecord.getId()) {
                row[storeName] = this._loadedRecord[storeName];
            }
        }
        this._loadedRecord = row;
        var store = row[this.relation]();
        if (!store.loaded) {
            store.loaded = true;
            if (!row.phantom) {
                store.load();
            }
        }

        //if both model has HayMany assocication and child model has BelongsTo associacion
        //we set the child models the parent model instance wich they will use for getXxx
        //that way both share the same object
        var belongsToAssoc;
        store.model.prototype.associations.each(function(assoc) {
            if (assoc instanceof Ext4.data.association.BelongsTo) {
                Ext4.ClassManager.get(assoc.model).prototype.associations.each(function(i) {
                    if (i instanceof Ext4.data.association.HasMany
                        && i.model == store.model.$className
                        && i.foreignKey == assoc.foreignKey
                    ) {
                        belongsToAssoc = assoc;
                    }
                }, this);

            }
        }, this);
        if (belongsToAssoc) {
            if (!store['belongsTo'+belongsToAssoc.instanceName]) {
                store.loadRecords = Ext4.Function.createInterceptor(store.loadRecords, function(records) {
                    var store = this;
                    for (var i=0; i < records.length; i++) {
                        records[i][belongsToAssoc.instanceName] = store['belongsTo'+belongsToAssoc.instanceName];
                    }
                });
                store.insert = Ext4.Function.createInterceptor(store.insert, function(index, records) {
                    var store = this;
                    if (!Ext4.isIterable(records)) {
                        records = [records];
                    }
                    for (var i=0; i < records.length; i++) {
                        records[i][belongsToAssoc.instanceName] = store['belongsTo'+belongsToAssoc.instanceName];
                    }
                });
            }
            store['belongsTo'+belongsToAssoc.instanceName] = row;
            store.each(function(r) {
                r[belongsToAssoc.instanceName] = row;
            }, this);
        }

        this.gridController.grid.bindStore(store);
    },

    reset: function()
    {
        this._loadedRecord = null;
        this.gridController.grid.unbindStore();
    },

    isDirty: function()
    {
        if (!this.gridController.grid.getStore()) return false;
        return this.gridController.grid.getStore().getModifiedRecords().length || this.gridController.grid.getStore().getNewRecords().length;
    },

    isValid: function()
    {
        return true;
    },

    _reloadLoadedRow: function()
    {
        var r = this.getLoadedRecord();
        if (!r.phantom) {
            r.self.load(r.getId(), {
                success: function(loadedRow) {
                    r.beginEdit();
                    r.set(loadedRow.getData());
                    r.endEdit();
                    r.commit();
                },
                scope: this
            });
        }
    },

    save: function(syncQueue)
    {
        if (this.gridController.grid.getStore()) {
            if (syncQueue) {
                syncQueue.add(this.gridController.grid.getStore());
                if (this.reloadRowOnSave) {
                    syncQueue.on('finished', function() {
                        this._reloadLoadedRow();

                    }, this, { single: true });
                }
            } else {
                this.gridController.grid.getStore().sync({
                    callback: function() {
                        if (this.reloadRowOnSave) {
                            this._reloadLoadedRow();
                        }
                    },
                    scope: this
                });
            }
        }
    },

    getLoadedRecord: function()
    {
        return this._loadedRecord;
    },

    enable: function()
    {
        this.gridController.grid.enable();
    },
    disable: function()
    {
        this._loadedRecord = null;
        var s = this.gridController.grid.store;
        if (s) {
            this.gridController.grid.bindStore(Ext4.create('Ext.data.Store', {
                model: s.model
            }));
        }
        this.gridController.grid.disable();
    },
    getPanel: function()
    {
        return this.grid;
    }
});
