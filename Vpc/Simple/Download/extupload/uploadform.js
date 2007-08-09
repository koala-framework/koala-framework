Ext.namespace('Vpc.Simple');
Vpc.Simple.uploadform = function(renderTo, config)
{

	// set blank image to local file
	Ext.BLANK_IMAGE_URL = '../extjs/resources/images/default/s.gif';

	// run this function when document becomes ready
	Ext.onReady(function() {

		var iconPath = '../img/silk/icons/';

		Ext.QuickTips.init();

		alert ("war da");

		var upform = new Ext.ux.UploadForm('form-ct-in', {
			autoCreate: true
			, url: '/filetree/filetree.php'
			, method: 'post'
			, maxFileSize: 1048570
			, pgCfg: {
				uploadIdName: 'UPLOAD_IDENTIFIER'
				, uploadIdValue: 'auto'
				, progressBar: true
				, progressTarget: 'under'
				, interval: 1000
				, maxPgErrors: 10
				, options: {
					url: 'progress.php'
					, method: 'post'
	//				, callback: pgCallback
				}
			}
			, baseParams: {
				cmd:'upload'
				, path: 'root'
			}
		});


	}) // end of onReady

}
// end of file
