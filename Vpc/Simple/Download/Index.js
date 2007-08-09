Ext.namespace('Vpc.Simple.Download');
Vpc.Simple.Download.Index = function(renderTo, config)
{


	//Ext.get(renderTo).createChild("<d class=\"SWFUploadTarget\"><div id=\"SWFUploadTarget\"></div></swfuploadtarget>");
	Ext.get(renderTo).createChild(	"<div class=\"SWFUploadTarget\">" +
									"<div id=\"SWFUploadTarget\"></div>" +
									"<h4 id=\"queueinfo\">Queue is empty</h4>" +
									"<div id=\"SWFUploadFileListingFiles\"></div>" +
									"<br class=\"clr\" />" +
									"<a class=\"swfuploadbtn\" id=\"cancelqueuebtn\" href=\"javascript:cancelQueue();\">Cancel queue</a><div class=\"configurationexample\">" +
									"<div id=\"adwords\"></div></div>");




	swfu = new SWFUpload({
				upload_script : config.controllerUrl,
				target : "SWFUploadTarget",
				flash_path : "/assets/vps/Vpc/Simple/Download/jscripts/SWFUpload/SWFUpload.swf",
				allowed_filesize : 30720,	// 30 MB
				allowed_filetypes : "*.*",
				allowed_filetypes_description : "All files...",
				browse_link_innerhtml : "Upload",
				upload_link_innerhtml : "Upload queue",
				browse_link_class : "swfuploadbtn browsebtn",
				upload_link_class : "swfuploadbtn uploadbtn",
				flash_loaded_callback : 'swfu.flashLoaded',
				upload_file_queued_callback : "fileQueued",
				upload_file_start_callback : 'uploadFileStart',
				upload_progress_callback : 'uploadProgress',
				upload_file_complete_callback : 'uploadFileComplete',
				upload_file_cancel_callback : 'uploadFileCancelled',
				upload_queue_complete_callback : 'uploadQueueComplete',
				upload_error_callback : 'uploadError',
				upload_cancel_callback : 'uploadCancel',
				auto_upload : false
	});

}