<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


/**
 * Adds config column to product table.
 */
class MW_Setup_Task_ProductAddConfig extends MW_Setup_Task_Abstract
{
	private $_mysql = 'ALTER TABLE "mshop_product" ADD "config" TEXT NOT NULL AFTER "label"';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array();
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}

	/**
	 * Add column to table if it doesn't exist.
	 *
	 * @param string $stmt SQL statement to execute for adding columns
	 */
	protected function _process( $stmt )
	{
		$this->_msg( 'Adding config column to mshop_product', 0 );

		if( $this->_schema->tableExists( 'mshop_product' ) === true
			&& $this->_schema->columnExists( 'mshop_product', 'config' ) === false )
		{
			$this->_execute( $stmt );
			$this->_status( 'done' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}