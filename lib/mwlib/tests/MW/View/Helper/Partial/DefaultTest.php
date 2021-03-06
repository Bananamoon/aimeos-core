<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class MW_View_Helper_Partial_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$view = new MW_View_Default();
		$conf = new MW_Config_Array();
		$paths = array( __DIR__ => array( 'testfiles' ) );

		$this->_object = new MW_View_Helper_Partial_Default( $view, $conf, $paths );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testTransform()
	{
		$this->assertEquals( '', $this->_object->transform( '', 'partial.html' ) );
	}


	public function testTransformParams()
	{
		$this->assertEquals( 'test', $this->_object->transform( '', 'partial.html', array( 'testparam' => 'test' ) ) );
	}
}
