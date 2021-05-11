/*!
 * Theme Gruntfile
 * https://github.com/chaschin
 * @author Alexey Chaschin
 */

module.exports = function (grunt) {
	'use strict';

	// Force use of Unix newlines
	grunt.util.linefeed = '\n';

	RegExp.quote = function (string) {
		return string.replace(/[-\\^$*+?.()|[\]{}]/g, '\\$&');
	};

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		jqueryCheck: 'if (typeof jQuery === \'undefined\') { throw new Error(\'Plugin\\\'s JavaScript requires jQuery\') }\n\n',
		clean: {
			dist: ['<%= pkg.jsoutput %>', '<%= pkg.cssoutput %>']
		},
		sass: {
			dist: {
				options: {
					style: 'expanded'
				},
				files: [{
					expand: true,
					cwd: 'sass/',
					src: ['*.scss'],
					dest: '<%= pkg.cssoutput %>/',
					ext: '.css'
				}]
			}
		},
		autoprefixer: {
			options: {
				browsers: ['last 2 versions', 'Chrome > 25', 'Safari > 6', 'iOS 7', 'Firefox > 25'],
			},
			dist: {
				files: {
					'<%= pkg.cssoutput %>/client.css': '<%= pkg.cssoutput %>/client.css'
				}
			}
		},
		csscomb: {
			dynamic_mappings: {
				expand: true,
				cwd: '<%= pkg.cssoutput %>/',
				src: ['*.css', '!*.min.css'],
				dest: '<%= pkg.cssoutput %>/'
			}
		},
		cssmin: {
			options: {
				keepSpecialComments: 0,
				banner: '/* Plugin minified css file */'
			},
			core: {
				files: [{
					expand: true,
					cwd: '<%= pkg.cssoutput %>/',
					src: ['*.css', '!*.min.css', '!print.css', '!ie.css'],
					dest: '<%= pkg.cssdest %>/',
					ext: '.min.css'
				}]
			}
		},
		concat: {
			options: {
				banner: '<%= jqueryCheck %>',
				stripBanners: false
			},
			client: {
				src: [
					'js-dev/client.js',
				],
				dest: '<%= pkg.jsoutput %>/client.js'
			},
		},
		copy: {
			client: {
				src: 'js-dev/client.js',
				dest: '<%= pkg.jsoutput %>/client.js'
			},
		},
		uglify: {
			options: {
				preserveComments: false,
				sourceMap: false,
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
				'<%= grunt.template.today("yyyy-mm-dd") %> */',
			},
			plugin: {
				files: [{
					expand: true,
					cwd: '<%= pkg.jsoutput %>',
					src: '**/*.js',
					dest: '<%= pkg.jsdest %>',
					ext: '.min.js'
				}]
			}
		},
		watch: {
			sass: {
				files: 'sass/*.scss',
				tasks: 'dist-css'
			},
			scripts: {
				files: 'js-dev/*.js',
				tasks: 'dist-js'
			},
		}
	});

	// These plugins provide necessary tasks.
	require('load-grunt-tasks')(grunt, { scope: 'devDependencies' });
	require('time-grunt')(grunt);

	// JS distribution task.
	grunt.registerTask('dist-js', ['copy', 'uglify']);

	// CSS distribution task.
	grunt.registerTask('dist-css', ['sass', 'autoprefixer', 'csscomb', 'cssmin']);

	grunt.registerTask('dist', ['clean', 'dist-js', 'dist-css']);
	grunt.registerTask('default', ['dist']);
};