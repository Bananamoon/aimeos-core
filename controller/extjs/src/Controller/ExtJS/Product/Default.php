<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS product controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Product_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the product controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Product' );
	}


	/**
	 * Executes tasks after processing the items.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function finish( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$manager = $this->_getManager();
		$indexManager = MShop_Factory::createManager( $this->_getContext(), 'catalog/index' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.id', $params->items ) );
		$search->setSlice( 0, count( $params->items ) );

		foreach( $manager->searchItems( $search ) as $item ) {
			$indexManager->saveItem( $item );
		}

		$this->_clearCache( (array) $params->items );

		return array(
			'success' => true,
		);
	}


	/**
	 * Deletes an item or a list of items.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function deleteItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$ids = (array) $params->items;
		$context = $this->_getContext();
		$manager = $this->_getManager();


		$manager->deleteItems( $ids );


		foreach( array( 'catalog', 'product' ) as $domain )
		{
			$manager = MShop_Factory::createManager( $context, $domain . '/list' );

			$search = $manager->createSearch();
			$expr = array(
				$search->compare( '==', $domain . '.list.refid', $ids ),
				$search->compare( '==', $domain . '.list.domain', 'product' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', $domain . '.list.id' ) ) );

			$start = 0;

			do
			{
				$result = $manager->searchItems( $search );
				$manager->deleteItems( array_keys( $result ) );

				$count = count( $result );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count >= $search->getSliceSize() );
		}


		$this->_clearCache( $ids );

		return array(
				'items' => $params->items,
				'success' => true,
		);
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		if( $this->_manager === null ) {
			$this->_manager = MShop_Factory::createManager( $this->_getContext(), 'product' );
		}

		return $this->_manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MShop search key prefix
	 */
	protected function _getPrefix()
	{
		return 'product';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function _transformValues( stdClass $entry )
	{
		if( isset( $entry->{'product.datestart'} ) && $entry->{'product.datestart'} != '' ) {
			$entry->{'product.datestart'} = str_replace( 'T', ' ', $entry->{'product.datestart'} );
		} else {
			$entry->{'product.datestart'} = null;
		}

		if( isset( $entry->{'product.dateend'} ) && $entry->{'product.dateend'} != '' ) {
			$entry->{'product.dateend'} = str_replace( 'T', ' ', $entry->{'product.dateend'} );
		} else {
			$entry->{'product.dateend'} = null;
		}

		if( isset( $entry->{'product.config'} ) ) {
			$entry->{'product.config'} = (array) $entry->{'product.config'};
		}

		return $entry;
	}
}
