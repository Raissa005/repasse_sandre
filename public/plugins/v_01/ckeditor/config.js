/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.skin = 'office2013';
	// config.skin = 'moono-dark';
	// config.skin = 'bootstrapck';

	config.language = 'pt';
	// config.uiColor = '#ffffff';	
	
	config.extraPlugins = 'autogrow';
	config.autoGrow_minHeight = 200;
	config.autoGrow_maxHeight = 450;
	// config.autoGrow_bottomSpace = 50;
	// config.autoGrow_onStartup = true;
	config.removePlugins = 'resize';
	// config.removeButtons = 'PasteFromWord';

	// config.defaultLanguage = 'pt';
	config.toolbarGroups = [

		{
			name: 'basicstyles', groups: [
				'basicstyles',
				'cleanup'
			]
		},
		{
			name: 'paragraph', groups: [
				'list',
				'indent',
				// 'blocks',
				'align',
				// 'bidi'
			]
		},
		{ name: 'styles' },
		{ name: 'colors' },
		// { name: 'about' }
		'/',
		{
			name: 'clipboard', groups: [
				'clipboard',
				'undo'
			]
		},
		{
			name: 'editing', groups: [
				'find',
				'selection',
				'spellchecker'
			]
		},
		{ name: 'links' },
		{ name: 'insert' },

		// { name: 'forms' },
		// { name: 'tools' },

		{
			name: 'document', groups: [
				'mode',
				'document',
				'doctools'
			]
		},
		// { name: 'others' },	
	];
};
CKEDITOR.replaceClass = 'box-ckeditor';

CKEDITOR.replace('box-ckeditor');
