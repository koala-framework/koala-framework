// vim: ts=2:sw=2:nu:fdc=4:nospell

// Create user extensions namespace (Ext.ux)
Ext.namespace('Ext.ux');

/**
  * Ext.ux.UploadForm Extension Class
  *
  * @author  Ing. Jozef Sakalos
  * @version $Id: Ext.ux.UploadForm.js 72 2007-07-27 18:59:45Z jozo $
  *
  * @class Ext.ux.UploadForm
  * @extends Ext.tree.BasicForm
  * @constructor
  * Creates new Ext.ux.UploadForm
	*
	* @param {String/HTMLElement/Element} ct container
	* @param {Object} config
	*
	* @cfg {String} addIcon Add icon file name (defaults to add.png)
	* @cfg {Boolean/Object} autoCreate Set it to true, or DomHelper config, to create form element
	* @cfg {String} clearIcon Clear all icon file name (defaults to cross.png)
	* @cfg {Object} defaultAutoCreate default DomHelper object for autoCreate
	* @cfg {String} deleteIcon Delete icon file name (defaults to delete.png)
	* @cfg {String} failureIcon Failure icon file name (defaults to exclamation.png)
	* @cfg {Boolean} floating Set to true to create UploadForm in floating layer
	* @cfg {String} iconPath Path to icons, relative or absolute (defaults to '../img/silk/icons')
	* @cfg {Integer} maxFileSize Maximum file size in bytes (defaults to 0 = no limit)
	* @cfg {Object} pgCfg Progress configuration object. Do not pass if you don't want progress info.
	* @cfg {Object} pgCfg.map Progress properties mapping (see also defaultProgressMap)
	* @cfg {Integer} pgCfg.maxPgErrors Maximum errors before progress requests to server are stopped (defaults to 10)
	* @cfg {Object} pgCfg.options Options for progress request call
	* @cfg {Boolean} pgCfg.progressBar true to create progressBar
	* @cfg {Integer} pgCfg.interval Interval for sendig progress info request to server in ms (defaults to 1000)
	* @cfg {String/HTMLElement/Element} pgCfg.progressTarget Where to display progress details
	* @cfg {String} pgCfg.uploadIdName Name for the hidden field with upload Id
	* @cfg {String} pgCfg.uploadIdValue Value for the hidden field with upload Id. Set to 'auto' to auto-generate
	* @cfg {String} stopIcon Stop icon file name (defaults to control_stop.png)
	* @cfg {String} successIcon Success icon file name (defaults to accept.png)
	* @cfg {String} UploadForm Upload icon file name (defaults to arrow_up.png)
	* @cfg {String} url url to upload to
	* @cfg {Integer} buttonWidth Button width for add button in px
	* @cfg {String} fileCls css class to add to files. Has to match with file type css (defaults to 'file')
	* @cfg {Integer} width Width of the floating layer in px. Used only if floating:true (defaults to 200)
	* @cfg {Integer} maxNameLength File name is truncated with ... appended if longer than this (defaults to 18)
  */
Ext.ux.UploadForm = function(ct, config) {
	alert ("upload form");
	// {{{
	// setup autoCreate, container and el

	var autoCreate =
		true === config.autoCreate
		? this.defaultAutoCreate
		: 'object' === typeof config.autoCreate
			? config.autoCreate
			: false
	;
	var el = autoCreate ?  Ext.DomHelper.append(ct, autoCreate) : ct;
	ct = Ext.get(ct);
	ct.setStyle('position','relative');
	this.container = ct;
	// }}}
	// {{{
	// call parent constructor
	Ext.ux.UploadForm.superclass.constructor.call(this, el, config);
	// }}}
	// {{{
	// create layer if the form should float
	var wrap, showShadow;
	if(true === this.floating) {
		wrap = this.container.wrap({tag:'div', cls:'x-uf-layer'});
		this.layer = new Ext.Layer({
			shadow:'sides'
		}, wrap);
		this.layer.setWidth(this.width);
		this.container.addClass('x-uf-layer-form-ct');

		// event handlers
		showShadow = function() {
			if(this.layer && this.layer.isVisible()) {
				this.layer.shadow.show(this.layer.dom);
			}
		}
		this.on({
			fileadded: {scope:this, fn:showShadow}
			, fileremoved: {scope:this, fn:showShadow}
			, clearqueue: {scope:this, fn:showShadow}
		});
	}
	// }}}
	// {{{
	// create "storage" for inputs
	this.inputs = new Ext.util.MixedCollection();
	// }}}
	// {{{
	// icons
	this.iconPath = config && config.iconPath ? config.iconPath : '../img/silk/icons';

	// add icon
	this.addIcon = config && config.addIcon ? config.addIcon : 'add.png';
	this.addIcon = this.iconPath + '/' + this.addIcon;

	// delete icon
	this.deleteIcon = config && config.deleteIcon ? config.deleteIcon : 'delete.png';
	this.deleteIcon = this.iconPath + '/' + this.deleteIcon;

	// upload icon
	this.uploadIcon = config && config.uploadIcon ? config.uploadIcon : 'arrow_up.png';
	this.uploadIcon = this.iconPath + '/' + this.uploadIcon;

	// stop icon
	this.stopIcon = config && config.stopIcon ? config.stopIcon : 'control_stop.png';
	this.stopIcon = this.iconPath + '/' + this.stopIcon;

	// clear icon
	this.clearIcon = config && config.clearIcon ? config.clearIcon : 'cross.png';
	this.clearIcon = this.iconPath + '/' + this.clearIcon;

	// success icon
	this.successIcon = config && config.successIcon ? config.successIcon : 'accept.png';
	this.successIcon = this.iconPath + '/' + this.successIcon;

	// failure icon
	this.failureIcon = config && config.failureIcon ? config.failureIcon : 'exclamation.png';
	this.failureIcon = this.iconPath + '/' + this.failureIcon;

	// }}}
	// {{{
	// create hidden for max file size
	if(this.maxFileSize) {
		Ext.DomHelper.append(this.el, {
			tag:'input'
			, type:'hidden'
			, name:'MAX_FILE_SIZE'
			, value: this.maxFileSize
		});
	}
	// }}}
	// {{{
	// get progress target and add class to it
	if(this.pgCfg && this.pgCfg.progressTarget) {
		this.progressTarget = Ext.get(this.pgCfg.progressTarget);
		if(this.progressTarget) {
			this.progressTarget.addClass('x-uf-pginfo-ct');
		}
	}
	// }}}
	// {{{
	// create hidden for upload progress id
	if(this.pgCfg && this.pgCfg.uploadIdName) {
		this.uploadId = Ext.DomHelper.append(this.el, {
			tag:'input'
			, type:'hidden'
			, name: this.pgCfg.uploadIdName
			, value: this.pgCfg.uploadIdValue
		});
	}
	// }}}
	// {{{
	// create buttons
	this.createButtons();
	// }}}
	// {{{
	// create progress info (bar + text) if configured
	this.createProgressInfo();
	// }}}
	// {{{
	// create first input (other are created on the fly)
	this.createUploadInput();
	// }}}
	// {{{
	// install event handlers
	this.on({
		actioncomplete: {scope:this, fn:this.onSuccess}
		, actionfailed: {scope:this, fn:this.onFailure}
	});
	// }}}
	// {{{
	// init QuickTips
	Ext.QuickTips.init();
	// }}}
	// {{{
	// add events
	this.addEvents({

		// {{{
		/**
			* Fires when file is added to the queue
			* @event fileadded
			* @param {UploadForm} this
			* @param {String} auto-generated file id
			*/
		fileadded: true
		// }}}
		// {{{
		/**
			* Fires when file is removed from the queue
			* @event fileremoved
			* @param {UploadForm} this
			* @param {String} auto-generated file id
			*/
		, fileremoved: true
		// }}}
		// {{{
		/**
			* Fires when queue is cleared
			* @event clearqueue
			* @param {UploadForm} this
			*/
		, clearqueue: true
		// }}}
		// {{{
		/**
			* Fires when upload starts
			* @event startupload
			* @param {UploadForm} this
			*/
		, startupload: true
		// }}}
		// {{{
		/**
			* Fires when upload starts
			* @event stopupload
			* @param {UploadForm} this
			*/
		, stopupload: true
		// }}}
		// {{{
		/**
			* Fires on progress update
			* @event progress
			* @param {UploadForm} this
			* @param {Object} object with progress values
			* @param {Floag} value 0 - 1 for progress bar
			*/
		, progress: true
		// }}}

	});
	// }}}

}; // end of constructor

// extend BasicForm
Ext.extend(Ext.ux.UploadForm, Ext.form.BasicForm, {

	// {{{
	// defaults
	addText: 'Add'
	, buttonWidth: 78
	, clearAllText: 'Clear all'
	, defaultAutoCreate: {tag:'form', enctype:'multipart/form-data'}
	, fileCls: 'file'
	, maxFileSize: 0
	, maxNameLength: 18
	, pgEtaText: 'Rem. time'
	, pgSizeText: 'Size/Total'
	, pgSpeedAvgText: 'Avg. speed'
	, pgSpeedText: 'Speed'
	, stopText: 'Stop'
	, uploadProgressText: 'Upload progress'
	, uploadText: 'Upload'
	, width: 200
	// {{{
	/** Progress object with zeros */
	, zeroProgress: {
			bytes_uploaded: 0
			, bytes_total: 0
			, speed_last: 0
			, speed_average: 0
			, est_sec: 0
	}
	// }}}
	// {{{
	/**
		* defines default progress map
		* When overriding do not touch left side
		*/
	, defaultProgressMap: {
		time_start: 'time_start'
		, time_last: 'time_last'
		, speed_average: 'speed_average'
		, speed_last: 'speed_last'
		, bytes_uploaded: 'bytes_uploaded'
		, bytes_total: 'bytes_total'
		, files_uploaded: 'files_uploaded'
		, est_sec: 'est_sec'
	}
	// }}}
	// }}}
	// {{{
	/**
		* Appends row to the queue table to display the file
		* Override if you need another file display
		* @param {Element} inp Input with file to display
		*/
	, appendRow: function(inp) {
		var filename = inp.getValue();
		var o = {
			id:inp.id
			, fileCls: this.getFileCls(filename)
			, fileName: Ext.util.Format.ellipsis(filename.split(/[\/\\]/).pop(), this.maxNameLength)
			, fileQtip: filename
		}

		var t = new Ext.Template([
			'<tr id="r-{id}">'
			, '<td class="x-unselectable {fileCls} x-tree-node-leaf">'
			, '<img class="x-tree-node-icon" src="' + Ext.BLANK_IMAGE_URL + '">'
			, '<span class="x-uf-filename" unselectable="on" qtip="{fileQtip}">{fileName}</span>'
			, '</td>'
			, '<td id="m-{id}" class="x-uf-filedelete"><a id="d-{id}" href="#"><img src="' + this.deleteIcon + '"></a>'
			, '</td>'
			, '</tr>'
		]);

		// save row reference for future
		inp.row = t.append(this.tbody, o, true);
	}
	// }}}
	// {{{
	/**
		* Creates buttons
		* private
		*/
	, createButtons: function() {

		// create containers sturcture
		var ct = Ext.DomHelper.append(this.el, {
				tag:'div', cls:'x-uf-buttons-ct'
			, children:[
				{ tag:'div', cls:'x-uf-input-ct'
					, children: [
							{tag:'div', cls:'x-uf-bbtn-ct'}
						, {tag:'div', cls:'x-uf-input-wrap'}
					]
				}
				, {tag:'div', cls:'x-uf-wait'}
				, {tag:'div', cls:'x-uf-ubtn-ct'}
				, {tag:'div', cls:'x-uf-cbtn-ct'}
			]
		}, true);

		// save containers for future use
		this.buttonsWrap = ct;
		this.inputWrap = ct.select('div.x-uf-input-wrap').item(0);
		this.addBtnCt = ct.select('div.x-uf-input-ct').item(0);

		// add button
		var bbtnCt = ct.select('div.x-uf-bbtn-ct').item(0);
		this.browseBtn = new Ext.Button(bbtnCt, {
			text:this.addText + '...'
			, cls: 'x-btn-text-icon'
			, icon: this.addIcon
			, minWidth:this.buttonWidth
		});

		// upload button
		var ubtnCt = ct.select('div.x-uf-ubtn-ct').item(0);
		this.ubtnCt = ubtnCt;
		this.uploadBtn = new Ext.Button(ubtnCt, {
			icon: this.uploadIcon
			, cls: 'x-btn-icon'
			, tooltip: this.uploadText
			, scope: this
			, handler: this.startUpload
		});

		// clear all button
		var cbtnCt = ct.select('div.x-uf-cbtn-ct').item(0);
		this.cbtnCt = cbtnCt;
		this.clearBtn = new Ext.Button(cbtnCt, {
			icon: this.clearIcon
			, cls: 'x-btn-icon'
			, tooltip: this.clearAllText
			, scope: this
			, handler: this.clearQueue
		});

		// save wait icon container
		this.waitIcon = ct.select('div.x-uf-wait').item(0);
	}
	// }}}
	// {{{
	/**
		* Creates progress bar and progress target container if configured and resets progress info
		* private
		*/
	, createProgressInfo: function() {
		if(this.pgCfg && true === this.pgCfg.progressBar) {
			var wrap = Ext.DomHelper.append(this.el, {
				tag: 'div', cls: 'x-uf-progress-wrap', children: [{
					tag: 'div', cls: 'x-uf-progress', children: [{
						tag: 'div', cls: 'x-uf-progress-bar'
					}]
				}]
			}, true);
			this.progressBar = wrap.select('div.x-uf-progress-bar').item(0);
		}

		// create container from progress info
		if(this.pgCfg) {
			var pgInfoCreate = {tag:'div', cls:'x-uf-pginfo-ct'};
			var pgTargetPos = this.pgCfg.progressTarget;
			pgTargetPos = ('above' === pgTargetPos && !wrap) ? 'under' : pgTargetPos;
			if(this.pgCfg && this.pgCfg.progressTarget && !this.progressTarget) {
				switch(pgTargetPos) {
					case 'under':
					case 'below':
						this.progressTarget = Ext.DomHelper.append(this.el, pgInfoCreate, true);
					break;

					case 'above':
						this.progressTarget = Ext.DomHelper.insertBefore(wrap, pgInfoCreate, true);
					break;
				}
			}
		}

		// reset progress to zero
		this.updateProgress(0);
	}
	// }}}
	// {{{
	/**
		* Creates upload input
		* private
		* @return {Element} Created input element
		*/
	, createUploadInput: function() {

		var id = Ext.id();
		var inp = Ext.DomHelper.append(this.inputWrap, {
			tag:'input'
			, type:'file'
			, cls:'x-uf-input'
			, size:1
			, id:id
			, name:id
		}, true);
		inp.on('change', this.onFileAdded, this);
		this.inputs.add(inp);
		this.fireEvent('fileadded', this, id);
		return inp;
	}
	// }}}
	// {{{
	/**
		* Processes progress info received from the server
		* Callback specified in this.pgCfg takes precedence
		*
		* @param {Object} options
		* @param {Boolean} bSuccess
		* @param {Object} response Server response
		*/
	, defaultProgressCallback: function(options, bSuccess, response) {

		// is another call in progress?
		if(this.processingProgress) {
			return;
		}

		// start of non-interference zone
		this.processingProgress = true;

		var o;
		try {
			o = Ext.decode(response.responseText) || {};
		}
		catch(e) {}

		// we have valid data from server and we're uploading
		if(o && true === o.success && this.uploading) {
			this.updateProgress(o);
			this.pgErrors = 0;
		}

		// we don't have valid data or we're not uploading
		else {
			this.pgErrors = this.pgErrors || 0;
			this.pgErrors++;
			if((this.pgCfg.maxPgErrors || 10) < this.pgErrors) {
				this.stopProgress();
			}
		}

		// end of non-interference zone
		this.processingProgress = false;
	}
	// }}}
	// {{{
	/**
		* Finds hidden iframe created by Ext that is form submit target
		* private
		*/
	, findIframe: function() {

		this.iframe = Ext.get(document.body).select('iframe.x-hidden').item(0);
		if(this.uploading && !this.iframe) {
			this.findIframe.defer(200, this);
		}
	}
	// }}}
	// {{{
	/**
		* Formats progress object before it is used by template
		* override this function if you want different formatting
		* @param {Object} o Object containing progress values
		* @return {Object} Object with formatted progress values
		*/
	, formatProgress: function(o) {

		// new return object
		var ro = {};

		var a = this.formatBytes(o.bytes_uploaded);
		ro.bytes_uploaded = a[0] + ' ' + a[1];

		a = this.formatBytes(o.bytes_total);
		ro.bytes_total = a[0] + ' ' + a[1];

		a = this.formatBytes(o.speed_last);
		ro.speed_last = a[0] + ' ' + a[1] + '/s';

		a = this.formatBytes(o.speed_average);
		ro.speed_average = a[0] + ' ' + a[1] + '/s';

		ro.est_sec = this.formatTime(o.est_sec);

		return(ro);
	}
	// }}}
	// {{{
	/**
		* Formats raw bytes to kB/mB/GB/TB
		* formating is decadic not binary
		* override this function if you want different format
		* @param {Integer} bytes
		* @return {Array} [value, unit]
		*/
	, formatBytes: function(bytes) {
		if(isNaN(bytes)) {
			return ['', ''];
		}
		var unit, val;
		if(999 > bytes) {
			unit = 'B';
			val = bytes;
		}
		else if(999999 > bytes) {
			unit = 'kB';
			val = Math.round(bytes/1000);
		}
		else if(999999999 > bytes) {
			unit = 'MB';
			val = Math.round(bytes/100000) / 10;
		}
		else if(999999999999 > bytes) {
			unit = 'GB';
			val = Math.round(bytes/100000000) / 10;
		}
		else {
			unit = 'TB';
			val = Math.round(bytes/100000000000) / 10;
		}

		return [val, unit];
	}
	// }}}
	// {{{
	/**
		* Formats time to hh:mm:ss omitting hh: if zero
		* override this function if you want different time format
		* @param {Integer} seconds Seconds to format
		* @return {String} Formatted time
		*/
	, formatTime: function(seconds) {
		var s = m = h = 0;
		if(3599 < seconds) {
			h = parseInt(seconds/3600);
			seconds -= h * 3600;
		}
		if(59 < seconds) {
			m = parseInt(seconds/60);
			seconds -= m * 60;
		}

		m = String.leftPad(m, 2, 0);
		h = String.leftPad(h, 2, 0);
		s = String.leftPad(seconds, 2, 0);

		return ("00" !== h ? h + ':' : '') + m + ':' + s;
	}
	// }}}
	// {{{
	/**
		* returns file class based on name extension
		* private
		* @param {String} name File name to get class of
		*/
	, getFileCls: function(name) {
		var atmp = name.split('.');
		if(1 === atmp.length) {
			return this.fileCls;
		}
		else {
			return this.fileCls + '-' + atmp.pop();
		}
	}
	// }}}
	// {{{
	/**
		* Creates template to display progress info
		* Override this for different formats/data
		*
		* @return {Template}
		*/
	, getProgressTemplate: function() {
		var tpl = new Ext.Template(
				'<table class="x-uf-pginfo-table"><tbody>'
				, '<tr><td class="x-uf-pginfo-label">' + this.pgSizeText + ':</td>'
				, '<td class="x-uf-pginfo-value">{bytes_uploaded}/{bytes_total}</td></tr>'
				, '<tr><td class="x-uf-pginfo-label">' + this.pgSpeedText + ':</td>'
				, '<td class="x-uf-pginfo-value">{speed_last}</td></tr>'
				, '<tr><td class="x-uf-pginfo-label">' + this.pgSpeedAvgText + ':</td>'
				, '<td class="x-uf-pginfo-value">{speed_average}</td></tr>'
				, '<tr><td class="x-uf-pginfo-label">' + this.pgEtaText + ':</td>'
				, '<td class="x-uf-pginfo-value">{est_sec}</td></tr>'
				, '</tbody></table>'
		);
		tpl.compile();
		return tpl;
	}
	// }}}
	// {{{
	/**
		* Hides the form (only if floating)
		* @param {Boolean/Element} animEl (optional) true for the default animation or a standard Element animation config object
		*/
	, hide: function(animEl) {
		if(this.layer) {
			this.layer.hide(animEl);
		}
	}
	// }}}
	// {{{
	/**
		* Called when delete icon is clicked
		* private
		*
		* @param {Event} e
		* @param {Element} target Target clicked
		*/
	, onDeleteFile: function(e, target) {
		this.removeFile(target.id.substr(2));
	}
	// }}}
	// {{{
	/**
		* File added event handler
		* @param {Event} e
		* @param {Element} inp Added input
		*/
	, onFileAdded: function(e, inp) {

		// hide all previous inputs
		this.inputs.each(function(i) {
			i.setDisplayed(false);
		});

		// create table to hold the file queue list
		if(!this.table) {
			this.table = Ext.DomHelper.append(this.el, {
				tag:'table', cls:'x-uf-table'
				, children: [ {tag:'tbody'} ]
			}, true);
			this.tbody = this.table.select('tbody').item(0);

			this.table.on({
				click:{scope:this, fn:this.onDeleteFile, delegate:'a'}
			});
		}

		// add input to internal collection
		var inp = this.inputs.itemAt(this.inputs.getCount() - 1);

		// uninstall event handler
		inp.un('change', this.onFileAdded, this);

		// append input to display queue table
		this.appendRow(inp);

		// create new input for future use
		this.createUploadInput();

	}
	// }}}
	// {{{
	/**
		* Success form submit event handler
		* private
		*
		* @param {Ext.ux.UploadForm} this
		* @param {Ext.form.Action} action Action object
		*/
	, onSuccess: function(form, action) {
		this.processResponse(form, action);
	}
	// }}}
	// {{{
	/**
		* Failure form submit event handler
		* private
		*
		* @param {Ext.ux.UploadForm} this
		* @param {Ext.form.Action} action Action object
		*/
	, onFailure: function(form, action) {
		this.processResponse(form, action);
	} // end of function onFailure
	// }}}
	// {{{
	/**
		* Processes both success and failure response from server
		* @param {Form} form Form than has been submitted
		* @param {Action} action Action that has been executed (submit)
		*/
	, processResponse: function(form, action) {
		this.stopUpload();
		var o = action.response.responseText ? Ext.decode(action.response.responseText) : {};

		// iterate through all inputs
		this.inputs.each(function(inp) {
			var msgTarget = Ext.get('m-' + inp.id);

			// no error processing
			if(!o.errors || !o.errors[inp.id]) {
				if(msgTarget) {
					msgTarget.update('<img src="' + this.successIcon + '">');
				}
				// do not remove last empty input
				if('' !== inp.getValue()) {
					// mark input for removal
					inp.markRemove = true;
				}
			}

			// an error processing
			else if(o.errors[inp.id]) {
				if(msgTarget) {
					msgTarget.update(
						'<img src="' + this.failureIcon + '"'
						+ ' qtip="' + o.errors[inp.id] + '"'
						+ ' qclass="x-form-invalid-tip"'
						+ ' ext:width="200"'
						+ '>'
					);
				}
			}
		}, this);

		// remove successfully uploaded files
		this.inputs.each(function(inp) {
			if(true === inp.markRemove) {
				this.removeFile(inp.id);
			}
		}, this);

	}
	// }}}
	// {{{
	/**
		* Remaps raw to internal progress object
		* private
		* @param {Object} o Raw progress object as received from the server
		* @return {Object} Object with internal progress values
		*/
	, remapProgress: function(o) {
		o = o || {};
		var map = this.pgCfg.map || this.defaultProgressMap;
		var p, onew = {};

		for(p in map) {
			onew[p] = o[map[p]] || ''
		}

		return onew;
	}
	// }}}
	// {{{
	/**
		* Removes all files from the queue
		* Individual remove events are supressed
		* private
		*/
	, clearQueue: function() {
		if(this.uploading) {
			return;
		}
		this.waitIcon.setDisplayed('none');
		this.updateProgress(0);
		this.inputs.each(function(inp) {
			if(!inp.isVisible()) {
				this.removeFile(inp.id, true);
			}
		}, this);

		this.fireEvent('clearqueue', this);
	}
	// }}}
	// {{{
	/**
		* Removes file from the queue
		* private
		*
		* @param {String} id Id of the file to remove (id is auto generated)
		* @param {Boolean} suppresEvent Set to true not to fire event
		*/
	, removeFile: function(id, suppresEvent) {
		if(this.uploading) {
			return;
		}
		var inp = this.inputs.get(id);
		if(inp && inp.row) {
			inp.row.remove();
		}
		if(inp) {
			inp.remove();
		}
		this.inputs.removeKey(id);
		if(true !== suppresEvent) {
			this.fireEvent('fileremoved', this, id);
		}
	}
	// }}}
	// {{{
	/**
		* Removes iframe created by Ext
		* private
		*/
	, removeIframe: function() {
		if(this.iframe) {
			this.iframe.remove();
		}
	}
	// }}}
	// {{{
	/**
		* Sends progress request to the server
		* private
		*/
	, requestProgress: function() {
		var conn = new Ext.data.Connection().request(this.pgCfg.options);
	}
	// }}}
	// {{{
	/**
		* Disables/Enables the whole form by masking/unmasking it
		*
		* @param {Boolean} disable true to disable, false to enable
		* @param {Boolean} alsoUpload true to disable also upload button
		*/
	, setDisabled: function(disable, alsoUpload) {

		if(disable) {
			this.addBtnCt.mask();
			if(true === alsoUpload) {
				this.ubtnCt.mask();
			}
			this.cbtnCt.mask();
		}
		else {
			this.addBtnCt.unmask();
			this.ubtnCt.unmask();
			this.cbtnCt.unmask();
		}
	}
	// }}}
	// {{{
	/**
		* Shows form, if floating, at a position
		* @param {Array} xy position
		* @param {Boolean/Element} animEl animation element
		*/
	, showAt: function(xy, animEl) {
		if(this.layer) {
			this.layer.setXY(xy);
			this.layer.show(animEl);
		}
	}
	// }}}
	// {{{
	/**
		* Starts querying server for progress info
		* private
		*/
	, startProgress: function() {
		var p = this.pgCfg
		if(p) {
			if(this.uploadId) {
				if('auto' === p.uploadIdValue) {
					this.uploadId.value = parseInt(Math.random() * 1e10);
				}
				p.options.params = p.options.params || {};
				p.options.params[p.uploadIdName] = this.uploadId.value;
			}
			p.options.scope = p.options.scope || this;
			p.options.callback = p.options.callback || this.defaultProgressCallback;
			this.timerId = setInterval(this.requestProgress.createDelegate(this), p.interval || 1000);
		}
	}
	// }}}
	// {{{
	/**
		* Starts the upload
		* private
		*/
	, startUpload: function() {
		if(2 > this.inputs.getCount()) {
			return;
		}
		this.updateProgress(0);
		if(this.uploading) {
			this.stopUpload();
			return;
		}
		this.uploading = true;
		this.waitIcon.setDisplayed('block');
		this.startProgress();
		this.setDisabled(true);
		this.updateUploadBtn();
		this.submit({url:this.url});
		this.findIframe();

		this.fireEvent('startupload', this);
	}
	// }}}
	// {{{
	/**
		* Stops querying server for progress info
		* private
		*/
	, stopProgress: function() {
		if(this.timerId) {
			clearInterval(this.timerId);
		}
	}
	// }}}
	// {{{
	/**
		* Stops the upload
		*/
	, stopUpload: function() {
		if(this.iframe) {
			try {
				this.iframe.dom.contentWindow.stop();
				this.removeIframe.defer(250, this);
			}
			catch(e) {}
		}
		this.uploading = false;
		this.setDisabled(false);
		this.waitIcon.setDisplayed('none');
		this.stopProgress();
		this.updateProgress(1);
		this.updateUploadBtn();

		this.fireEvent('stopupload', this);
	}
	// }}}
	// {{{
	/**
		* Displays upload or stop icon depending on uploading state
		*/
	, updateUploadBtn: function() {
		this.uploadBtn.setIcon(this.uploading ? this.stopIcon : this.uploadIcon);
		this.uploadBtn.setQtip(this.uploading ? this.stopText : this.uploadText);
	}
	// }}}
	// {{{
	/**
		* Updates upload progress information
		* takes into account existence of progressBar and progressTarget
		* Raw progress object is remapped using progressMap
		*
		* @param {Integer/Object} value 0 = clear, 1 = done, object with raw progress values
		* @return void
		*/
	, updateProgress: function(value) {

		// declare o
		var o;

		// reset the progress
		if(0 === value) {
			o = Ext.apply({}, this.zeroProgress);
		}

		// ensure that everything that should be is 100%
		if(1 === value && this.lastPgObj) {
			o = this.lastPgObj;
			o.bytes_uploaded = o.bytes_total;
			o.est_sec = 0;
		}

		if('object' === typeof value) {
			o = this.remapProgress(value);
			value = o.bytes_total ? o.bytes_uploaded / o.bytes_total : 0;
		}

		// save remapped progress object for future use
		this.lastPgObj = o;

		// update progress bar if we have one
		var pp;
		if(this.progressBar) {
			pp = Ext.get(this.progressBar.dom.parentNode);
			this.progressBar.setWidth(Math.floor(value * pp.dom.offsetWidth));
		}

		// update progress target if we have one
		if(this.progressTarget) {
			this.getProgressTemplate().overwrite(this.progressTarget, this.formatProgress(o));
		}
		else if(this.pgCfg && 'qtip' === this.pgCfg.progressTarget && this.progressBar) {
			Ext.QuickTips.tips({
				target: this.progressBar
				, title: this.uploadProgressText
				, text: this.getProgressTemplate().apply(this.formatProgress(o))
				, width: 160
				, autoHide: true
			});
			Ext.QuickTips.enable();
		}

		this.fireEvent('progress', this, o, value);
	}
	// }}}

});

// useful button methods: setIcon and setQtip
// {{{
Ext.override(Ext.Button, {
	setIcon: function(icon) {
		this.icon = icon;
		this.el.select('button').item(0).setStyle('background-image', 'url(' + this.icon + ')');
	}
	, setQtip: function(qtip) {
		if(qtip) {
			this.tooltip = qtip;
			if(typeof this.tooltip == 'object'){
				Ext.QuickTips.tips(Ext.apply({
				target: btnEl.id
				}, this.tooltip));
			} else {
				this.el.select('button').item(0).dom[this.tooltipType] = this.tooltip;
			}
		}
	}
});
// }}}

// end of file
