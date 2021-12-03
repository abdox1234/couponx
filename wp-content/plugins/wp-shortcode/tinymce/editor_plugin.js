(function() {
	tinymce.create('tinymce.plugins.wpspanel', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mcewpspanel', function() {
				ed.windowManager.open({
					file : url + '/editor_plugin.php',
					inline : 1,
					width : 450 + ed.getLang('wpspanel.delta_width', 0),
                    height : 80 + ed.getLang('wpspanel.delta_height', 0)
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});
            // Resize function
            ed.addCommand( 'mcewpspanel_resize', function( ui, v ) {
                if (ed.windowManager.params === undefined) {
                    // TinyMCE 4
                    var windowID = ed.windowManager.windows[0]._id;
                    var elem = window.top.document.getElementById(windowID+'-body');
                    jQuery(elem).height(v.height + 52);
                } else {
                    // TinyMCE 3
                    ed.windowManager.params.mce_height = v.height + 52;
                }
                
            } );
			// Register example button
			ed.addButton('wpspanel', {
				title : 'Add Custom Shortcode',
				cmd : 'mcewpspanel',
				image : url + '/shortcode.png'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('wpspanel', n.nodeName == 'IMG');
			});
            
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

        getInfo : function() {
            return {
                longname : 'WP Shortcode',
                author : 'MyThemeShop',
                authorurl : 'http://mythemeshop.com',
                infourl : 'http://mythemeshop.com/plugins/wp-shortcode',
                version : '1.4.2'
            };
        }
	});

	// Register plugin
	tinymce.PluginManager.add('wpspanel', tinymce.plugins.wpspanel);
})();