// vim: ts=2:sw=2:nu:fdc=4:nospell

// Create user extensions namespace (Ext.ux)
Ext.namespace('Ext.ux');

/**
  * Ext.ux.SWFUpload Extension Class
  *
  * @author  Ing. Jozef Sakalos
  * @version $Id: Ext.ux.SWFUpload.js 66 2007-07-25 23:14:47Z jozo $
  *
  * @class Ext.ux.SWFUpload
  * @extends Ext.util.Observable
  * @constructor
  * Creates new Ext.ux.SWFUpload
	*
	* @cfg {String} flashPath Path to swfupload.swf (required, defaults to "./SWFUpload.swf")
	* @cfg {String} flashId id for embed/object tag. Auto generated if not set.
  */
Ext.ux.SWFUpload = function() {

	var Impl = function() {
		// {{{
		// icons
		this.iconPath = '../img/silk/icons';

		// add icon
		this.addIcon = 'add.png';

		// delete icon
		this.deleteIcon = 'delete.png';

		// upload icon
		this.uploadIcon = 'arrow_up.png';

		// stop icon
		this.stopIcon = 'control_stop.png';

		// success icon
		this.successIcon = 'accept.png';

		// failure icon
		this.failureIcon = 'exclamation.png';
		// }}}
			// {{{
			// create total progress info object
			this.pgInfo = {
				totalBytes: 0
				, doneBytes: 0
				, lastBytes: 0
			};
			// }}}
	};

	Ext.extend(Impl, Ext.util.Observable, {
		// {{{
		// defaults
		flashPath: 'SWFUpload.swf'
		, flashColor: '#000000'
		, flashWidth: '1px'
		, flashHeight: '1px'
		, buttonWidth: 78
		, maxNameLength: 18
		, fileCls: 'file'
		, sizeText: 'Size'
		, errorText: 'Error'
		, pgSizeText: 'Size/Total'
		, pgSpeedText: 'Speed'
		, pgSpeedAvgText: 'Avg. speed'
		, pgEtaText: 'Rem. time'
		// }}}
		// {{{
		, create: function(el, config) {

			// try to remove background flicker in IE
			try {
				document.execCommand('BackgroundImageCache', false, true);
			} catch(e) {}

			// apply passed config to this
			Ext.apply(this, config);

			// the ui is created inside of this element
			this.el = el;

			// flash related (IE doesn't like - (dash) in the id of the flash object
			this.flashId = this.flashId || Ext.id().replace(/-/, '');

			// apply defaults to fv (flashvars) object
			this.fv = this.fv || {};
			Ext.applyIf(this.fv, {
				// todo: find out if uploadScript can be changed on the fly (it contains upload path)
				uploadScript: ''
				, allowedFiletypesDescription: 'All files...'
				, flashLoadedCallback: 'Ext.ux.SWFUpload.onFlashLoaded'
				, uploadFileQueuedCallback: 'Ext.ux.SWFUpload.onFileQueued'
				, uploadFileStartCallback: 'Ext.ux.SWFUpload.onFileUploadStart'
				, uploadProgressCallback: 'Ext.ux.SWFUpload.onProgress'
				, uploadFileCompleteCallback: 'Ext.ux.SWFUpload.onFileUploadComplete'
				, uploadQueueCompleteCallback: 'Ext.ux.SWFUpload.onQueueComplete'
				, uploadDialogCancelCallback: 'Ext.ux.SWFUpload.onDialogCancel'
				, uploadFileErrorCallback: 'Ext.ux.SWFUpload.onFileError'
				, uploadFileCancelCallback: 'Ext.ux.SWFUpload.onFileCancel'
				, uploadQueueCancelCallback: 'Ext.ux.SWFUpload.onQueueCancel'
				, autoUpload: false
				, allowedFiletypes: '*.*'
				, maximumFilesize: 1024
			});

			// create flash object if needed	
			var flashHtml;
			if(!this.flashCt) {
				this.flashCt = Ext.DomHelper.append(document.body, {
					tag:'div'
					, style:'width:0px;height:0px;position:absolute;top:0px;left:0px'
				}, true);
				flashHtml = Ext.DomHelper.createTemplate(this.getFlashCreate()).html;

				if(this.debug) {
					console.log(flashHtml);
				}

				this.flashCt.dom.innerHTML = flashHtml;
				this.flash = Ext.getDom(this.flashId);
			}

			this.createButtons();
			this.createProgressInfo();
			this.createQueueTable();

		}
		// }}}
		// {{{
		, destroy: function() {
			if(this.flashCt) {
				this.flashCt.remove();
				this.flashCt = null;
			}
			this.purgeListeners();
		}
		// }}}

		// flash commands
		// {{{
		, browse: function() {
			if(this.flash) {
				this.flash.browse();
			}
		}
		// }}}
		// {{{
		, upload: function() {
			if(this.flash) {
				this.flash.upload();
			}
		}
		// }}}
		// {{{
		, cancelFile: function(fileId) {
			if(this.flash) {
				this.flash.cancelFile(fileId);
			}
		}
		// }}}
		// {{{
		, cancelQueue: function() {
			if(this.flash) {
				this.flash.cancelQueue();
			}
			var i;
			if(this.tbody) {
				while(this.tbody.dom.rows.length) {
					this.tbody.dom.deleteRow(0);
				}
			}
			this.pgInfo.totalBytes = 0;
			this.pgInfo.doneBytes = 0;
			this.pgInfo.lastBytes = 0;
			this.updateProgress(0);
		}
		// }}}
		// end of flash commands

		// flash callbacks
		// {{{
		, onFlashLoaded: function(bLoaded) {
			if(this.debug && bLoaded) {
				console.log('Flash loaded');
			}
			this.cancelQueue.defer(100, this);
		}
		// }}}
		// {{{
		, onFileQueued: function(file, queueLength) {
			this.appendRow(file, queueLength);
			this.pgInfo.totalBytes += file.size;
			this.updateProgress(0);
		}
		// }}}
		// {{{
		, onFileUploadStart: function(file, position, queueLength) {
//			debugger;
		}
		// }}}
		// {{{
		, onFileUploadComplete: function(file) {
			this.pgInfo.doneBytes += file.size - this.pgInfo.lastBytes;
			this.pgInfo.lastBytes = 0;

			var icon = Ext.get('icn_' + file.id);
				if(icon) {
				icon.update(
					'<img src="' + this.iconPath + '/' + this.successIcon + '"'
					+ '>'
				);
			}

			this.updateFileProgress(file, 1);
			this.updateProgress();
		}
		// }}}
		// {{{
		, onProgress: function(file, bytesUploaded, bytesTotal) {

			// calculate progress
			this.pgInfo.doneBytes += bytesUploaded - this.pgInfo.lastBytes;
			this.pgInfo.lastBytes = bytesUploaded;

			this.updateProgress();
			this.updateFileProgress(file, bytesUploaded/bytesTotal);
		}
		// }}}
		// {{{
		, onDialogCancel: function() {
//			debugger;
		}
		// }}}
		// {{{
		, onFileError: function(errCode, file, errMsg) {
			var asize;
			switch(errCode) {
				case -10: // HTTP error
				break;

				case -20: // No upload script
				break;

				case -30: // IOError
				break;

				case -40: // Security error
				break;

				case -50: // File too big
					asize = this.formatBytes(file.size);
					alert(errMsg + '\n\nFile: ' + file.name + ', Size: ' + asize[0] + ' ' + asize[1]);
				break;
			}
//			Ext.Msg.alert(this.errorText, errMsg);
			var icon = Ext.get('icn_' + file.id);
			if(icon) {
				icon.update(
					'<img src="' + this.iconPath + '/' + this.successIcon + '"'
					+ 'qtip="' + errMsg + '"'
					+ 'qclass="x-form-invalid-tip"'
					+ 'ext:width="200"'
					+ '>'
				);
			}

		}
		// }}}
		// {{{
		, onFileCancel: function(file, queueLength) {

			// remove file info row
			var fir = Ext.fly('fir_' + file.id);
			fir.remove();

			// file progress row
			var fpr = Ext.fly('fpr_' + file.id);
			fpr.remove();

			this.pgInfo.totalBytes -= file.size;
			this.updateProgress(0);
		}
		// }}}
		// {{{
		, onQueueCancel: function() {
//			debugger;
		}
		// }}}
		// {{{
		, onQueueComplete: function() {
			this.pgInfo.doneBytes = this.pgInfo.totalBytes;
			this.pgInfo.lastBytes = 0;
			this.updateProgress(1);
			// this gets called also on queueCancel
//			debugger;
		}
		// }}}
		// end of flash callbacks

		// {{{
		, getFlashCreate: function() {

			var createObj = {};
			// create object for 
			if(Ext.isGecko) {
				createObj = {
					tag:'embed'
					, type:'application/x-shockwave-flash'
					, src:this.flashPath
					, width:this.flashWidth
					, height:this.flashHeight
					, id:this.flashId
					, name:this.flashId
					, bgcolor:this.flashColor
					, quality:'high'
					, wmode:'transparent'
					, menu:'false'
					, flashvars:this.getFlashVars()
				};
			}

			else {
				createObj = {
					tag:'object'
					, id:this.flashId
					, classid:'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'
					, width:this.flashWidth
					, height:this.flashHeight
					, children:[
							{tag:'param', name:'movie', value:this.flashPath}
						, {tag:'param', name:'bgcolor', value:'#000000'}
						, {tag:'param', name:'quality', value:'high'}
						, {tag:'param', name:'wmode', value:'transparent'}
						, {tag:'param', name:'menu', value:'false'}
						, {tag:'param', name:'flashvars', value:this.getFlashVars()}
					]
				};
			}

			return createObj;

		}
		// }}}
		// {{{
		, getFlashVars: function() {
			var prop, avars = [];
			for(prop in this.fv) {
				avars.push(prop + '=' + this.fv[prop]);
			}
			return avars.join('&');
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
				text:'Add...'
				, cls: 'x-btn-text-icon'
				, icon: this.iconPath + '/' + this.addIcon
				, minWidth:this.buttonWidth
				, scope:this
				, handler:this.browse
			});
			
			// upload button
			var ubtnCt = ct.select('div.x-uf-ubtn-ct').item(0);
			this.ubtnCt = ubtnCt;
			this.uploadBtn = new Ext.Button(ubtnCt, {
				icon: this.iconPath + '/' + this.uploadIcon
				, cls: 'x-btn-icon'
				, tooltip: 'Upload'
				, scope: this
				, handler: this.upload
			});

			// clear all button
			var cbtnCt = ct.select('div.x-uf-cbtn-ct').item(0);
			this.cbtnCt = cbtnCt;
			this.clearBtn = new Ext.Button(cbtnCt, {
				icon: this.iconPath + '/' + this.deleteIcon
				, cls: 'x-btn-icon'
				, tooltip: 'Clear all'
				, scope: this
				, handler: this.cancelQueue
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
			var wrap;
			if(this.pgCfg && true === this.pgCfg.progressBar) {
				wrap = Ext.DomHelper.append(this.el, {
					tag: 'div', cls: 'x-uf-progress-wrap', children: [{
						tag: 'div', cls: 'x-uf-progress', children: [{
							tag: 'div', cls: 'x-uf-progress-bar'
						}]
					}]
				}, true);
				this.progressBar = wrap.select('div.x-uf-progress-bar').item(0);
			}

			// create container from progress info
			var pgInfoCreate, pgTargetPos;
			if(this.pgCfg) {
				pgInfoCreate = {tag:'div', cls:'x-uf-pginfo-ct'};
				pgTargetPos = this.pgCfg.progressTarget;
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
		, createQueueTable: function() {
			// create table to hold the file queue list
			this.table = Ext.DomHelper.append(this.el, {
				tag:'table', cls:'x-uf-table'
				, children: [ {tag:'tbody'} ]
			}, true);
			this.tbody = this.table.select('tbody').item(0);

			this.table.on({
				click:{scope:this, fn:this.onDeleteFile, delegate:'a'}
			});
		}
		// }}}
		// {{{
		/**
			* Appends row to the queue table to display the file
			* private
			*
			* @param {Element} inp Input with file to display
			*/
		, appendRow: function(file, queueLength) {

			// get formatted size array
			var asize = this.formatBytes(file.size);

			// create data object for template
			var o = {
				id:file.id
				, fileCls: this.getFileCls(file.name)
				, fileName: Ext.util.Format.ellipsis(file.name.split(/[\/\\]/).pop(), this.maxNameLength)
				, fileQtip: file.name
				, fileSize: asize[0]
				, fileSizeUnit: asize[1]
			};

			// create file info template
			// todo: this could be set as class variable for the case of override
			// fir = file info row
			// icn = icon (on the right) (for setting class with bg image)
			// lnk = link (on the right)
			var fileTpl = new Ext.Template([
				'<tr class="x-uf-filerow" id="fir_{id}">'
				, '<td class="x-unselectable {fileCls} x-tree-node-leaf">'
				, '<img class="x-tree-node-icon" src="' + Ext.BLANK_IMAGE_URL + '">'
				, '<span class="x-uf-filename" unselectable="on"'
				, ' qtip="<h3>{fileQtip}</h3>' + this.sizeText + ': {fileSize} {fileSizeUnit}"'
				, '>{fileName}</span>'
				, '</td>'
				, '<td id="icn_{id}" class="x-uf-filedelete"><a id="lnk_{id}" href="#"><img src="' 
					+ this.iconPath + '/' + this.deleteIcon + '"></a>'
				, '</td>'
				, '</tr>'
			]);

			// append row with file info to the tbody
			fileTpl.append(this.tbody, o, true);

			// create file progress bar template
			// fpr = file progress row
			// fpb = file progress bar
			var pgTpl = new Ext.Template([
				'<tr id="fpr_{id}">'
				, '<td colspan="3">'
				, '<div id="fpb_{id}" class="x-uf-filepb">'
				, '</div></td></tr>'
			]);

			// append row with progress bar to the tbody
			pgTpl.append(this.tbody, {id:file.id});

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
		var s = 0;
		var m = 0;
		var h = 0;
		if(3599 < seconds) {
			h = parseInt(seconds/3600, 10);
			seconds -= h * 3600;
		}
		if(59 < seconds) {
			m = parseInt(seconds/60, 10);
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
			* Called when delete icon is clicked
			* private
			* 
			* @param {Event} e
			* @param {Element} target Target clicked
			*/
		, onDeleteFile: function(e, target) {
			this.cancelFile(target.id.substr(4));
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
			var o = {
				bytes_uploaded: 0
				, bytes_total: this.pgInfo.totalBytes
				, speed_last: 0
				, speed_average: 0
				, est_sec: 0
			};

			// ensure that everything that should be is 100%
			if(1 === value) {
				o.bytes_uploaded = o.bytes_total;
				o.est_sec = 0;
			}

			o.bytes_uploaded = this.pgInfo.doneBytes;
			
			value = o.bytes_total ? o.bytes_uploaded / o.bytes_total : 0;

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
					, title: 'Upload progress'
					, text: this.getProgressTemplate().apply(this.formatProgress(o))
					, width: 160
					, autoHide: true
				});
				Ext.QuickTips.enable();
			}

		}
		// }}}
		// {{{
		, updateFileProgress: function(file, val) {
			// file progress bar
			var fpb = Ext.get('fpb_' + file.id);
			var fullWidth;
			if(fpb) {
				fullWidth = fpb.dom.parentNode.offsetWidth;
				fpb.setWidth(Math.floor(val * fullWidth));
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

	});

	return new Impl();

}(); // end of SWFUpload singleton

// {{{
Ext.override(Ext.Button, {
	setIcon: function(icon) {
		this.icon = icon;
		this.el.select('button').item(0).setStyle('background-image', 'url(' + this.icon + ')');
	}
	, setQtip: function(qtip) {
		if(qtip) {
			this.tooltip = qtip;
			if(typeof this.tooltip === 'object'){
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
