<?php
App::uses('AssetFilter', 'AssetCompress.Lib');

/**
 * Output minifier for uglify-j
 *
 * Requires nodejs and uglify-js to be installed.
 *
 * @see https://github.com/mishoo/UglifyJS
 */
class Uglifyjs extends AssetFilter {

	protected $_settings = array(
		'node' => '/usr/local/bin/node',
		'uglify' => '/usr/local/bin/uglifyjs',
		'node_path' => '/usr/local/lib/node_modules'
	);

/**
 * Allow some settings to also be defined in bootstrap.php
 * @param array $settings The settings from asset_compress.ini
 */
	public function settings($settings) {
		$config = array_intersect_key((array)Configure::read('AssetCompress'),
			array('node' => true, 'node_path' => true, 'uglify' => true));
		parent::settings(array_merge($settings, $config));
	}

/**
 * Run `uglifyjs` against the output and compress it.
 *
 * @param string $filename Name of the file being generated.
 * @param string $input Th4 uncompressed contents for $filename.
 * @return string Compressed contents.
 */
	public function output($filename, $input) {
		$cmd = $this->_settings['node'] . ' ' . $this->_settings['uglify'] . ' - ';
		$env = array('NODE_PATH' => $this->_settings['node_path']);
		return $this->_runCmd($cmd, $input, $env);
	}
}
