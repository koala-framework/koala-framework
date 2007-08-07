/*Ext.namespace('Vpc.Simple.Image');
Vpc.Simple.Image.Index = function(renderTo, config)
{

	this.dialogControllerUrl = config.dialogControllerUrl;



	 this.renderTo = renderTo;
	 this.layout = new Ext.BorderLayout(this.renderTo, {
                north: {split: false, initialSize: 30},
                center: { autoScroll: true }
            });
	 this.layout.beginUpdate();


	 var ToolbarContentPanel = new Ext.ContentPanel({autoCreate: true, fitToFrame:true, closable:false});
	 this.layout.add('north', ToolbarContentPanel);

	 var FormContentPanel = new Ext.ContentPanel({autoCreate: true, fitToFrame:true, closable:false});
     this.layout.add('center', FormContentPanel);

	 this.layout.endUpdate();




	 var tb = new Ext.Toolbar(ToolbarContentPanel.getEl());

	 var submitButton = new Ext.Toolbar.Button({
		          text: 'Hochladen',
		          handler: this.submit,
				  scope: this
				  });
	 tb.add(submitButton);

	 var resetButton = new Ext.Toolbar.Button({
		          text: 'Reset',
		          handler: this.reset,
				  scope: this
				  });

	 tb.add(resetButton);




   this.form = new Ext.form.Form({
       labelWidth: 50,
				labelAlignt: 'right',
				name: 'uploadForm',
				fileUpload : true,
				method: 'POST',
				url:config.controllerUrl + 'jsonUpload/',
				baseParams: { action : 'upload' }
    });
    this.form.add(
        new Ext.form.TextField({
					fieldLabel: 'Datei',
					name: 'pictureUpload1',
					id: 'pictureUpload1',
					inputType: 'file',
					width: 200,
					allowBlank: true
		})
    );

    this.form.render(FormContentPanel.getEl());

}

Ext.extend(Vpc.Simple.Image.Index, Ext.util.Observable,
{

	submit : function (){

		/*this.dialog = new Ext.BasicDialog(Ext.get(this.renderTo).createChild(), {
				        modal:true,
				        autoTabs:true,
				        width:500,
				        height:300,
				        shadow:true,
				        minWidth:300,
				        minHeight:300
	    });

		//form = new Vps.Auto.Form.Dialog(null, {controllerUrl: this.dialogControllerUrl})
		this.dialog = new Vps.Auto.Form(null, {controllerUrl: this.dialogControllerUrl,
		                                              fileUpload: true})
		this.dialog.show();
		//this.dialog.addKeyListener(27, this.dialog.hide, this.dialog);
        //this.dialog.addButton('Schliessen', this.dialog.hide, this.dialog);
		//this.dialog.addButton('Endgueltig hochladen', this.upload, this);
        //this.dialog.show();
		//dialog = new Ext.Dialog...
		//form = new Vps.Auto.Form(dialog, {controllerUrl: /component/edit/Vpc_Simple_Image_IndexForm/1/})

	},

	reset : function (){
		var form = this.form;
		form.reset();
	},

	upload : function (){
		var form = this.form;
		if (form.isValid()) {

            Ext.MessageBox.wait("Lädt gerade hoch...", 'Bitte warten');
            form.submit({
                    // callback handler if submit has been successful
                    success:function(form, action){
                        Ext.MessageBox.hide();
                        Ext.MessageBox.alert('Success', 'fertig');
                    },

                    // callback handler if submit has failed
                    failure: function(form, action) {
                        Ext.MessageBox.hide();
                        Ext.MessageBox.alert('Failure', 'Ein Fehler ist aufgetreten');
                    },
                    //params : { action : formAction }
            });
        }

		this.dialog.hide();
	}

})*/






