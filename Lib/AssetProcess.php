<?php
/**
 * Thin wrapper around proc_open() so Filters
 * don't have to directly fiddle with that API.
 */
class AssetProcess {

	protected $_env = null;

	protected $_command = '';

	protected $_error;

	protected $_output;

/**
 * Get/set the environment for the command.
 *
 * @param array $env Environment variables.
 * @return The environment variables that are set, or
 *    this.
 */
	public function environment($env = null) {
		if ($env !== null) {
			$this->_env = $env;
			return $this;
		}
		return $this->_env;
	}

/**
 *  Copies the named variable from $_ENV to $this->_env only if it
 *  does not already exist in $this->_env.
 * 
 * @param string $var The environment variable name.
 */
	private function __inheritEnv($var) {
		if (isset($_ENV[$var]) && isset($this->_env[$var])) {
			$this->_env[$var] = $_ENV[$var];
		}
	}

/**
 * Run the command and capture the output as the return.
 *
 * @param string $input STDIN for the command.
 * @param string Output from the command.
 */
	public function run($input = null) {
		// node.exe on Windows must have systemdrive and systemroot set
		$this->__inheritEnv('SystemDrive');
		$this->__inheritEnv('SystemRoot');

		$descriptorSpec = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);
		$process = proc_open(
			$this->_command,
			$descriptorSpec,
			$pipes,
			null,
			$this->_env
		);
		if (is_resource($process)) {
			fwrite($pipes[0], $input);
			fclose($pipes[0]);

			$this->_output = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			$this->_error = stream_get_contents($pipes[2]);
			fclose($pipes[2]);
			proc_close($process);
		}
		return $this->_output;
	}

/**
 * Get the STDERR from the process.
 *
 * @return string Content from the command.
 */
	public function error() {
		return $this->_error;
	}

/**
 * Get the STDOUT from the process.
 *
 * @return string Content from the command.
 */
	public function output() {
		return $this->_output;
	}

/**
 * Set the command that will be run.
 *
 * @param string $command Command name to run.
 * @return $this
 */
	public function command($command) {
		// wrap executable name in quotes if necessary for Windows (.exe)
		$command = preg_replace('/^\s*([^"\s]+\s.+\.exe)(\s|$)/', '"$1"', $command);
		$this->_command = $command;
		return $this;
	}
}
