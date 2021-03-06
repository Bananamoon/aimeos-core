<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class MShop_Service_Provider_Decorator_AbstractTest extends PHPUnit_Framework_TestCase
{
	private $_mock;
	private $_object;
	private $_context;


	protected function setUp()
	{
		$this->_context = TestHelper::getContext();

		$servManager = MShop_Service_Manager_Factory::createManager( $this->_context );
		$search = $servManager->createSearch();
		$search->setConditions($search->compare('==', 'service.provider', 'Default'));
		$result = $servManager->searchItems($search, array('price'));

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order base item found' );
		}

		$this->_mock = $this->getMockBuilder( 'MShop_Service_Provider_Payment_PrePay' )
			->setConstructorArgs( array( $this->_context, $item ) )
			->setMethods( array( 'calcPrice', 'checkConfigBE', 'checkConfigFE', 'getConfigBE',
				'getConfigFE', 'injectGlobalConfigBE', 'isAvailable', 'isImplemented', 'query',
				'setCommunication', 'setConfigFE', 'updateAsync', 'updateSync' ) )
			->getMock();

		$this->_object = new MShop_Service_Provider_Decorator_Test( $this->_context, $item, $this->_mock );
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


	public function testCalcPrice()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->_context )->getSubManager( 'base' )->createItem();

		$this->_mock->expects( $this->once() )->method( 'calcPrice' )->will( $this->returnValue( $item->getPrice() ) );

		$this->assertInstanceOf( 'MShop_Price_Item_Interface', $this->_object->calcPrice( $item ) );
	}


	public function testCheckConfigBE()
	{
		$this->_mock->expects( $this->once() )->method( 'checkConfigBE' )->will( $this->returnValue( array() ) );

		$this->assertEquals( array(), $this->_object->checkConfigBE( array() ) );
	}


	public function testCheckConfigFE()
	{
		$this->_mock->expects( $this->once() )->method( 'checkConfigFE' )->will( $this->returnValue( array() ) );

		$this->assertEquals( array(), $this->_object->checkConfigFE( array() ) );
	}


	public function testGetConfigBE()
	{
		$this->_mock->expects( $this->once() )->method( 'getConfigBE' )->will( $this->returnValue( array() ) );

		$this->assertEquals( array(), $this->_object->getConfigBE() );
	}


	public function testGetConfigFE()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->_context )->getSubManager( 'base' )->createItem();

		$this->_mock->expects( $this->once() )->method( 'getConfigFE' )->will( $this->returnValue( array() ) );

		$this->assertEquals( array(), $this->_object->getConfigFE( $item ) );
	}


	public function testInjectGlobalConfigBE()
	{
		$this->_mock->expects( $this->once() )->method( 'injectGlobalConfigBE' );

		$this->_object->injectGlobalConfigBE( array() );
	}


	public function testIsAvailable()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->_context )->getSubManager( 'base' )->createItem();

		$this->_mock->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );

		$this->assertEquals( true, $this->_object->isAvailable( $item ) );

	}

	public function testIsImplemented()
	{
		$this->_mock->expects( $this->once() )->method( 'isImplemented' )->will( $this->returnValue( true ) );

		$this->assertTrue( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_QUERY ) );
	}


	public function testQuery()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->_context )->createItem();

		$this->_mock->expects( $this->once() )->method( 'query' );

		$this->_object->query( $item );
	}


	public function testSetCommunication()
	{
		$this->_mock->expects( $this->once() )->method( 'setCommunication' );

		$this->_object->setCommunication( new MW_Communication_Curl() );
	}


	public function testSetConfigFE()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->_context )
			->getSubManager( 'base' )->getSubManager( 'service' )->createItem();

		$this->_mock->expects( $this->once() )->method( 'setConfigFE' );

		$this->_object->setConfigFE( $item, array() );
	}


	public function testUpdateAsync()
	{
		$this->_mock->expects( $this->once() )->method( 'updateAsync' );

		$this->_object->updateAsync();
	}


	public function testUpdateSync()
	{
		$this->_mock->expects( $this->once() )->method( 'updateSync' );

		$response = null; $header = array();
		$this->_object->updateSync( array(), 'body', $response, $header );
	}


	public function testCallInvalid()
	{
		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->_object->invalid();
	}
}


class MShop_Service_Provider_Decorator_Test extends MShop_Service_Provider_Decorator_Abstract
{

}
