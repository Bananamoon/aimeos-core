<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MAdmin
 * @subpackage Log
 */


/**
 * Default log item implementation.
 *
 * @package MAdmin
 * @subpackage Log
 */
class MAdmin_Log_Item_Default
	extends MShop_Common_Item_Abstract
	implements MAdmin_Log_Item_Interface
{
	private $_values;

	/**
	 * Initializes the log item.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = array() )
	{
		parent::__construct( 'log.', $values );

		$this->_values = $values;
	}


	/**
	 * Returns the facility of the item.
	 *
	 * @return string Returns the facility
	 */
	public function getFacility()
	{
		return ( isset( $this->_values['facility'] ) ? (string) $this->_values['facility'] : '' );
	}


	/**
	 * Sets the new facility of the of the item.
	 *
	 * @param string $facility Facility
	 */
	public function setFacility( $facility )
	{
		$this->_values['facility'] = (string) $facility;
		$this->setModified();
	}


	/**
	 * Returns the timestamp of the item.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimestamp()
	{
		return ( isset( $this->_values['timestamp'] ) ? (string) $this->_values['timestamp'] : null );
	}


	/**
	 * Returns the priority of the item.
	 *
	 * @return integer Returns the priority
	 */
	public function getPriority()
	{
		return ( isset( $this->_values['priority'] ) ? (int) $this->_values['priority'] : 0 );
	}


	/**
	 * Sets the new priority of the item.
	 *
	 * @param integer $priority Priority
	 */
	public function setPriority( $priority )
	{
		$this->_values['priority'] = (int) $priority;
		$this->setModified();
	}


	/**
	 * Returns the message of the item.
	 *
	 * @return string Returns the message
	 */
	public function getMessage()
	{
		return ( isset( $this->_values['message'] ) ? (string) $this->_values['message'] : '' );
	}


	/**
	 * Sets the new message of the item.
	 *
	 * @param string $message Message
	 */
	public function setMessage( $message )
	{
		$this->_values['message'] = (string) $message;
		$this->setModified();
	}


	/**
	 * Returns the request of the item.
	 *
	 * @return string Returns the request
	 */
	public function getRequest()
	{
		return ( isset( $this->_values['request'] ) ? (string) $this->_values['request'] : '' );
	}


	/**
	 * Sets the new request of the item.
	 *
	 * @param string $request Request
	 */
	public function setRequest( $request )
	{
		$this->_values['request'] = (string) $request;
		$this->setModified();
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'log.facility': $this->setFacility( $value ); break;
				case 'log.priority': $this->setPriority( $value ); break;
				case 'log.message': $this->setMessage( $value ); break;
				case 'log.request': $this->setRequest( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['log.facility'] = $this->getFacility();
		$list['log.timestamp'] = $this->getTimestamp();
		$list['log.priority'] = $this->getPriority();
		$list['log.message'] = $this->getMessage();
		$list['log.request'] = $this->getRequest();

		return $list;
	}
}
