<?php
App::uses('AssetProcess', 'AssetCompress.Lib');

class AssetProcessTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$testable = new ReflectionClass('AssetProcess');
		$this->_cmd = $testable->getProperty('_cmd');
		$this->_cmd->setAccessible(true);
		$this->_env = $testable->getProperty('_env');
		$this->_env->setAccessible(true);
		$this->process = new AssetProcess();
	}

	public function testWindowsCommandNames() {
		$command = 'C:\Program Files\nodejs\node.exe';
		$this->process->command($command);
		$expected = '"C:\Program Files\nodejs\node.exe"';
		$result = $this->_cmd->getValue($this->process);
		$this->assertEquals($expected, $result, "Wrap the default Windows location.");

		$command = 'C:\Program Files\nodejs\node.exe -e param';
		$this->process->command($command);
		$expected = '"C:\Program Files\nodejs\node.exe" -e param';
		$result = $this->_cmd->getValue($this->process);
		$this->assertEquals($expected, $result, "Wrap only the executable.");

		$command = 'C:\bin\nodejs\node.exe -e param';
		$this->process->command($command);
		$expected = 'C:\bin\nodejs\node.exe -e param';
		$result = $this->_cmd->getValue($this->process);
		$this->assertEquals($expected, $result, "Don't wrap unnecessarily.");

		$command = '/usr/bin/node param';
		$this->process->command($command);
		$expected = '/usr/bin/node param';
		$result = $this->_cmd->getValue($this->process);
		$this->assertEquals($expected, $result, "Unix commands unharmed.");
	}

}
