/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.language = 'ru';
    config.skin = 'bootstrapck';
    config.allowedContent = true;
    config.forceEnterMode = true;
    config.basicEntities = true;
    
	// config.uiColor = '#AADC6E';
    config.toolbarGroups = [
/*                {
                    name: 'document'
                    , groups: ['document', 'doctools']
                }
			,    */

                {
                    name: 'mode'
                },

            { name: 'editing',     groups: [ 'find' ] }
                , {
                    name: 'clipboard'
                    , groups: ['clipboard', 'undo']
                }
                , {
                    name: 'links'
                }
                , {
                    name: 'insert'
                }
                , {
                    name: 'others'
                }
				, '/'
                , {
                    name: 'basicstyles'
                    , groups: ['basicstyles', 'cleanup']
                }
                , {
                    name: 'paragraph'
                    , groups: ['list', 'indent', 'blocks', 'align']
                }
                , {
                    name: 'colors'
                }
                , {
                    name: 'tools'
                },
                {
    name: 'styles', groups: ['Styles','Format']
                }
                ]
}
