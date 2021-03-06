<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Catalog_Manager_Index_Catalog_Default.
 */
class MShop_Catalog_Manager_Index_Catalog_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new MShop_Catalog_Manager_Index_Catalog_Default( TestHelper::getContext() );
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


	public function testCleanup()
	{
		$this->_object->cleanup( array( -1 ) );
	}


	public function testAggregate()
	{
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'catalog' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}


		$search = $this->_object->createSearch( true );
		$result = $this->_object->aggregate( $search, 'catalog.index.catalog.id' );

		$this->assertEquals( 4, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 2, $result[$item->getId()] );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );

		$result = $productManager->searchItems( $search );

		if( ( $product = reset( $result ) ) === false ) {
			throw new Exception( 'No product item with code CNE found!' );
		}

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$listManager = $catalogManager->getSubManager( 'list' );
		$search = $listManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'catalog.list.domain', 'product' ) );
		$catListItems = $listManager->searchItems( $search );

		if( ( $catListItem = reset( $catListItems ) ) === false ) {
			throw new Exception( 'No catalog list item found!' );
		}


		//new product item
		$product->setId( null );
		$product->setCode( 'ModifiedCNC' );
		$productManager->saveItem( $product );

		//new catalog list item
		$catListItem->setId( null );
		$catListItem->setRefId( $product->getId() );
		$listManager->saveItem( $catListItem );

		$this->_object->saveItem( $product );


		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.catalog.id', $catListItem->getParentId() ) );
		$result = $this->_object->searchItems( $search );


		$this->_object->deleteItem( $product->getId() );
		$listManager->deleteItem( $catListItem->getId() );
		$productManager->deleteItem( $product->getId() );


		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.catalog.id', $catListItem->getParentId() ) );
		$result2 = $this->_object->searchItems( $search );


		$this->assertContains( $product->getId(), array_keys( $result ) );
		$this->assertFalse( in_array( $product->getId(), array_keys( $result2 ) ) );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$context = TestHelper::getContext();

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$catSearch = $catalogManager->createSearch();
		$catSearch->setConditions( $catSearch->compare( '==', 'catalog.label', 'Kaffee' ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catItem = reset( $result ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}

		$catSearch->setConditions( $catSearch->compare( '==', 'catalog.label', 'Neu' ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catNewItem = reset( $result ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}


		$search = $this->_object->createSearch();

		$search->setConditions( $search->compare( '==', 'catalog.index.catalog.id', $catItem->getId() ) ); // catalog ID
		$result = $this->_object->searchItems( $search, array() );

		$this->assertEquals( 2, count( $result ) );

		$search->setConditions( $search->compare( '!=', 'catalog.index.catalog.id', null ) ); // catalog ID
		$result = $this->_object->searchItems( $search, array() );

		$this->assertEquals( 8, count( $result ) );

		$func = $search->createFunction( 'catalog.index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setConditions( $search->compare( '>=', $func, 0 ) ); // position

		$sortfunc = $search->createFunction( 'sort:catalog.index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->_object->searchItems( $search, array() );

		$this->assertEquals( 2, count( $result ) );


		$catIds = array( (int) $catItem->getId(), (int) $catNewItem->getId() );
		$func = $search->createFunction( 'catalog.index.catalogcount', array( 'default', $catIds ) );
		$search->setConditions( $search->compare( '==', $func, 2 ) ); // count categories

		$result = $this->_object->searchItems( $search, array() );

		$this->assertEquals( 1, count( $result ) );
	}


	public function testCleanupIndex()
	{
		$this->_object->cleanupIndex( '0000-00-00 00:00:00' );
	}

}