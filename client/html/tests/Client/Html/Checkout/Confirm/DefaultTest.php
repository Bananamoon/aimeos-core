<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Checkout_Confirm_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_context->setEditor( 'UTC001' );

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Checkout_Confirm_Default( $this->_context, $paths );
		$this->_object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$this->_context->getSession()->set( 'arcavias/orderid', $this->_getOrder( '2011-09-17 16:14:32' )->getId() );

		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$this->_context->getSession()->set( 'arcavias/orderid', $this->_getOrder( '2011-09-17 16:14:32' )->getId() );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos checkout-confirm">', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( '$$$', '$$$' );
	}


	public function testProcess()
	{
		$this->_context->getSession()->set( 'arcavias/orderid', $this->_getOrder( '2011-09-17 16:14:32' )->getId() );

		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'code' => 'paypalexpress' ) );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
	}


	public function testProcessNoCode()
	{
		$this->_object->process();
	}


	protected function _getOrder( $date )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_context );

		$search = $orderManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $date ) );

		$result = $orderManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order found' );
		}

		return $item;
	}
}
