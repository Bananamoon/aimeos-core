<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Product_Manager_Factory.
 */
class MShop_Product_Manager_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testCreateManager()
	{
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $manager );
	}


	public function testCreateManagerName()
	{
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext(), 'Default' );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $manager );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Product_Exception' );
		MShop_Product_Manager_Factory::createManager( TestHelper::getContext(), '%^&' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( 'MShop_Exception' );
		MShop_Product_Manager_Factory::createManager( TestHelper::getContext(), 'unknown' );
	}
}