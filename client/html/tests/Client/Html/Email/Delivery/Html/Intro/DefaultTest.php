<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Email_Delivery_Html_Intro_DefaultTest extends PHPUnit_Framework_TestCase
{
	private static $_orderItem;
	private static $_orderBaseItem;
	private $_object;
	private $_context;
	private $_emailMock;


	public static function setUpBeforeClass()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$search = $orderManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );
		$result = $orderManager->searchItems( $search );

		if( ( self::$_orderItem = reset( $result ) ) === false ) {
			throw new Exception( 'No order found' );
		}

		self::$_orderBaseItem = $orderBaseManager->load( self::$_orderItem->getBaseId() );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_emailMock = $this->getMock( 'MW_Mail_Message_None' );

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Email_Delivery_Html_Intro_Default( $this->_context, $paths );

		$view = TestHelper::getView();
		$view->extOrderItem = self::$_orderItem;
		$view->extOrderBaseItem = self::$_orderBaseItem;
		$view->addHelper( 'mail', new MW_View_Helper_Mail_Default( $view, $this->_emailMock ) );

		$this->_object->setView( $view );
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
		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->_object->getBody();

		$this->assertStringStartsWith( '<p class="email-common-intro', $output );
		$this->assertContains( 'The delivery status of your order', $output );
	}


	public function testGetBodyDeliveryDispatched()
	{
		$orderItem = clone self::$_orderItem;
		$view = $this->_object->getView();

		$orderItem->setDeliveryStatus( MShop_Order_Item_Abstract::STAT_DISPATCHED );
		$view->extOrderItem = $orderItem;

		$output = $this->_object->getBody();

		$this->assertStringStartsWith( '<p class="email-common-intro', $output );
		$this->assertContains( 'has been dispatched', $output );
	}


	public function testGetBodyDeliveryRefused()
	{
		$orderItem = clone self::$_orderItem;
		$view = $this->_object->getView();

		$orderItem->setDeliveryStatus( MShop_Order_Item_Abstract::STAT_REFUSED );
		$view->extOrderItem = $orderItem;

		$output = $this->_object->getBody();

		$this->assertStringStartsWith( '<p class="email-common-intro', $output );
		$this->assertContains( 'could not be delivered', $output );
	}


	public function testGetBodyDeliveryReturned()
	{
		$orderItem = clone self::$_orderItem;
		$view = $this->_object->getView();

		$orderItem->setDeliveryStatus( MShop_Order_Item_Abstract::STAT_RETURNED );
		$view->extOrderItem = $orderItem;

		$output = $this->_object->getBody();

		$this->assertStringStartsWith( '<p class="email-common-intro', $output );
		$this->assertContains( 'We received the returned parcel', $output );
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
		$this->_object->process();
	}
}
