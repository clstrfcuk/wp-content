module.exports = function(grunt) {
	grunt.initConfig({
		uglify: {
			js: {
				files: {
					'admin/assets/js/admin.min.js': [
						//'admin/**/*.js',
						'!admin/auto-update/**',
						'framework/lib/**/*.js',
						'!framework/lib/**/*/borderRadius.js',
						'!framework/lib/**/*/margin.js',
						'!framework/lib/**/*/borderWidth.js',
						'!framework/lib/**/*/boxShadow.js',
						'!framework/lib/**/*/padding.js',
						'admin/assets/js/sweetalert.min.js',
						'admin/assets/js/frosty.js',
						'admin/assets/js/jquery.widget.min.js',
						'admin/assets/js/accordion.js',
						'admin/assets/js/admin.js',
						'modules/modal/assets/js/jquery.shuffle.modernizr.js',
						'modules/modal/assets/js/jquery.shuffle.min.js',
						'modules/modal/assets/js/shuffle-script.js'
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
						'!admin/assets/css/admin.min.css',
						'framework/lib/**/*.css',
						'admin/assets/css/sweetalert.css',
					]
				}
			}
		}
	});
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.registerTask('default', ['uglify:js','cssmin:css']);
};