module.exports = function(grunt) {
	grunt.initConfig({
		uglify: {
			js: {
				files: {
					'admin/assets/js/admin.min.js': [
						'!admin/auto-update/**',
						'framework/lib/**/*.js',
						'!framework/lib/**/*/borderRadius.js',
						'!framework/lib/**/*/margin.js',
						'!framework/lib/**/*/borderWidth.js',
						'!framework/lib/**/*/boxShadow.js',
						'!framework/lib/**/*/padding.js',
						'admin/assets/js/sweetalert.min.js',
						'admin/assets/js/jquery.widget.min.js',
						'admin/assets/js/accordion.js',
						'admin/assets/js/admin.js',
						'modules/assets/js/jquery.shuffle.modernizr.js',
						'modules/assets/js/jquery.shuffle.min.js',
						'modules/assets/js/shuffle-script.js'
					]
				}
			}
		},
		cssmin: {
			css: {
				files: {
					'admin/assets/css/admin.min.css': [
						'admin/**/*.css',
						'!admin/assets/css/sweetalert.css',
						'!admin/auto-update/**',
						'!admin/bsf-core/**',
						'!admin/assets/css/admin.min.css',
						'framework/lib/**/*.css',
						'admin/assets/css/sweetalert.css',
					]
				}
			}
		},
		copy: {
			main: {
				options: {
					mode: true
				},
				src: [
				'**',
				'!node_modules/**',
				'!modules/modal/node_modules/**',
				'!modules/info_bar/node_modules/**',
				'!modules/slide_in/node_modules/**',
				'!.git/**',
				'!*.sh',
				'!Gruntfile.js',
				'!package.json',
				'!.gitignore',
				'!convertplug.zip',
				'!Optimization.txt'
				],
				dest: 'convertplug/'
			}
		},
		compress: {
			main: {
				options: {
					archive: 'convertplug.zip',
					mode: 'zip'
				},
				files: [
				{
					src: [
					'./convertplug/**'
					]

				}
				]
			}
		},
		clean: {
			main: ["convertplug"],
			zip: ["convertplug.zip"],
		},
		makepot: {
            target: {
                options: {
                    domainPath: '/',
                    mainFile: 'convertplug.php',
                    potFilename: 'lang/smile.pot',
                    potHeaders: {
                        poedit: true,
                        'x-poedit-keywordslist': true
                    },
                    type: 'wp-plugin',
                    updateTimestamp: true
                }
            }
        },
        addtextdomain: {
            options: {
                textdomain: 'smile',
            },
            target: {
                files: {
                    src: ['*.php', '**/*.php', '!node_modules/**', '!php-tests/**', '!bin/**', '!admin/bsf-core/**']
                }
            }
        }

	});

	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
	grunt.loadNpmTasks('grunt-contrib-clean');

	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-wp-i18n');

	grunt.registerTask('default', ['uglify:js','cssmin:css']);
	grunt.registerTask('release', ['clean:zip', 'copy','compress','clean:main']);
	grunt.registerTask('i18n', ['makepot']);
};
