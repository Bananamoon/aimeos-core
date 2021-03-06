<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Jobs_Product_Import_Csv_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_context;
	private $_arcavias;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$this->_context = TestHelper::getContext();
		$this->_arcavias = TestHelper::getArcavias();
		$config = $this->_context->getConfig();

		$config->set( 'controller/jobs/product/import/csv/skip-lines', 1 );
		$config->set( 'controller/jobs/product/import/csv/location', __DIR__ . '/_testfiles/valid' );

		$this->_object = new Controller_Jobs_Product_Import_Csv_Default( $this->_context, $this->_arcavias );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		MShop_Factory::setCache( false );
		MShop_Factory::clear();

		$this->_object = null;

		if( file_exists( 'tmp/import.zip' ) ) {
			unlink( 'tmp/import.zip' );
		}
	}


	public function testGetName()
	{
		$this->assertEquals( 'Product import CSV', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Imports new and updates existing products from CSV files';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$prodcodes = array( 'job_csv_test', 'job_csv_test2' );
		$nondelete = array( 'attribute', 'product' );
		$delete = array( 'media', 'price', 'text' );

		$convert = array(
			1 => 'Text/LatinUTF8',
		);

		$this->_context->getConfig()->set( 'controller/jobs/product/import/csv/converter', $convert );

		$this->_object->run();

		$result = $this->_get( $prodcodes, array_merge( $delete, $nondelete ) );
		$properties = $this->_getProperties( array_keys( $result ) );
		$this->_delete( $prodcodes, $delete, $nondelete );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 2, count( $properties ) );

		foreach( $result as $product ) {
			$this->assertEquals( 5, count( $product->getListItems() ) );
		}
	}


	public function testRunUpdate()
	{
		$prodcodes = array( 'job_csv_test', 'job_csv_test2' );
		$nondelete = array( 'attribute', 'product' );
		$delete = array( 'media', 'price', 'text' );

		$this->_object->run();
		$this->_object->run();

		$result = $this->_get( $prodcodes, array_merge( $delete, $nondelete ) );
		$properties = $this->_getProperties( array_keys( $result ) );
		$this->_delete( $prodcodes, $delete, $nondelete );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 2, count( $properties ) );

		foreach( $result as $product ) {
			$this->assertEquals( 5, count( $product->getListItems() ) );
		}
	}


	public function testRunProcessorInvalidPosition()
	{
		$prodcodes = array( 'job_csv_test', 'job_csv_test2' );

		$mapping = array(
			'item' => array(
				0 => 'product.code',
				1 => 'product.label',
				2 => 'product.type',
				3 => 'product.status',
			),
			'text' => array(
				4 => 'text.type',
				5 => 'text.content',
				100 => 'text.type',
				101 => 'text.content',
			),
			'media' => array(
				8 => 'media.url',
			),
		);

		$this->_context->getConfig()->set( 'controller/jobs/product/import/csv/mapping', $mapping );

		$this->_object->run();

		$this->_delete( $prodcodes, array( 'text', 'media' ), array() );
	}


	public function testRunProcessorInvalidMapping()
	{
		$mapping = array(
			'media' => array(
					8 => 'media.url',
			),
		);

		$this->_context->getConfig()->set( 'controller/jobs/product/import/csv/mapping', $mapping );

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->run();
	}


	public function testRunProcessorInvalidData()
	{
		$mapping = array(
			'item' => array(
				0 => 'product.code',
				1 => 'product.label',
				2 => 'product.type',
			),
			'text' => array(
				3 => 'text.type',
				4 => 'text.content',
			),
			'media' => array(
				5 => 'media.url',
				6 => 'product.list.type',
			),
			'price' => array(
				7 => 'price.type',
				8 => 'price.value',
				9 => 'price.taxrate',
			),
			'attribute' => array(
				10 => 'attribute.type',
				11 => 'attribute.code',
			),
			'product' => array(
				12 => 'product.code',
				13 => 'product.list.type',
			),
			'property' => array(
				14 => 'product.property.type',
				15 => 'product.property.value',
			),
		);

		$this->_context->getConfig()->set( 'controller/jobs/product/import/csv/mapping', $mapping );

		$config = $this->_context->getConfig();
		$config->set( 'controller/jobs/product/import/csv/skip-lines', 0 );
		$config->set( 'controller/jobs/product/import/csv/location', __DIR__ . '/_testfiles/invalid' );

		$this->_object = new Controller_Jobs_Product_Import_Csv_Default( $this->_context, $this->_arcavias );

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->run();
	}


	public function testRunBackup()
	{
		$config = $this->_context->getConfig();
		$config->set( 'controller/jobs/product/import/csv/container/type', 'Zip' );
		$config->set( 'controller/jobs/product/import/csv/location', 'tmp/import.zip' );
		$config->set( 'controller/jobs/product/import/csv/backup', 'tmp/test-%Y-%m-%d.zip' );

		if( copy( __DIR__ . '/_testfiles/import.zip', 'tmp/import.zip' ) === false ) {
			throw new Exception( 'Unable to copy test file' );
		}

		$this->_object->run();

		$filename = strftime( 'tmp/test-%Y-%m-%d.zip' );
		$this->assertTrue( file_exists( $filename ) );

		unlink( $filename );
	}


	public function testRunBackupInvalid()
	{
		$config = $this->_context->getConfig();
		$config->set( 'controller/jobs/product/import/csv/container/type', 'Zip' );
		$config->set( 'controller/jobs/product/import/csv/location', 'tmp/import.zip' );
		$config->set( 'controller/jobs/product/import/csv/backup', 'tmp/notexist/import.zip' );

		if( copy( __DIR__ . '/_testfiles/import.zip', 'tmp/import.zip' ) === false ) {
			throw new Exception( 'Unable to copy test file' );
		}

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->run();
	}


	protected function _delete( array $prodcodes, array $delete, array $nondelete )
	{
		$catListManager = MShop_Catalog_Manager_Factory::createManager( $this->_context )->getSubmanager( 'list' );
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$listManager = $productManager->getSubManager( 'list' );

		foreach( $this->_get( $prodcodes, $delete + $nondelete ) as $id => $product )
		{
			foreach( $delete as $domain )
			{
				$manager = MShop_Factory::createManager( $this->_context, $domain );

				foreach( $product->getListItems( $domain ) as $listItem )
				{
					$manager->deleteItem( $listItem->getRefItem()->getId() );
					$listManager->deleteItem( $listItem->getId() );
				}
			}

			foreach( $nondelete as $domain )
			{
				$ids = array_keys( $product->getListItems( $domain ) );
				$listManager->deleteItems( $ids );
			}

			$productManager->deleteItem( $product->getId() );

			$search = $catListManager->createSearch();
			$search->setConditions( $search->compare( '==', 'catalog.list.refid', $id ) );
			$result = $catListManager->searchItems( $search );

			$catListManager->deleteItems( array_keys( $result ) );
		}


		$attrManager = MShop_Attribute_Manager_Factory::createManager( $this->_context );

		$search = $attrManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.code', 'import-test' ) );

		$result = $attrManager->searchItems( $search );

		$attrManager->deleteItems( array_keys( $result ) );
	}


	protected function _get( array $prodcodes, array $domains )
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $prodcodes ) );

		return $productManager->searchItems( $search, $domains );
	}


	protected function _getProperties( array $prodids )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context )->getSubManager( 'property' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.property.parentid', $prodids ) );
		$search->setSortations( array( $search->sort( '+', 'product.property.type.code' ) ) );

		return $manager->searchItems( $search );
	}
}