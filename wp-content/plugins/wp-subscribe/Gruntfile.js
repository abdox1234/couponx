module.exports = function(grunt) {

    // Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// Generate POT files.
		makepot: {
			dist: {
				options: {
					type: 'wp-plugin',
					mainFile: 'wp-subscribe-pro.php',
					domainPath: 'languages',
					potHeaders: {
						'report-msgid-bugs-to': 'https://community.mythemeshop.com/',
						'language-team': 'MyThemeShop <support-team@mythemeshop.com>'
					},
					potFilename: 'wp-subscribe-pro.pot',
					exclude: [
						'node_modules/.*'
					]
				}
			}
		},

		// Check textdomain errors.
        checktextdomain: {
            options: {
                // Keywords specs wordpress
                keywords: [
                    '__:1,2d',
                    '_e:1,2d',
                    '_x:1,2c,3d',
                    'esc_html__:1,2d',
                    'esc_html_e:1,2d',
                    'esc_html_x:1,2c,3d',
                    'esc_attr__:1,2d',
                    'esc_attr_e:1,2d',
                    'esc_attr_x:1,2c,3d',
                    '_ex:1,2c,3d',
                    '_n:1,2,4d',
                    '_nx:1,2,4c,5d',
                    '_n_noop:1,2,3d',
                    '_nx_noop:1,2,3c,4d'
                ]
            },

			plugin: {
                //cwd: '/',
                src: ['**/*.php', '!node_modules/**'],
                expand: true,
                options: {
                    text_domain: ['wp-subscribe']
                }
            }
        }
	});

	// Default task(s).
    grunt.registerTask('default', [
		'checktextdomain', 'makepot'
    ]);

    // Load the plugin that provides the tasks.
	grunt.loadNpmTasks( 'grunt-wp-i18n' );

	// Quality Assurance
    grunt.loadNpmTasks( 'grunt-checktextdomain' );
};
