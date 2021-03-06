<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


require_once 'phing/Task.php';

/**
 * Generating jsb2 package files for core and extensions.
 */
class MShopJsbPackageTask extends Task
{
	private $_arcavias;
	private $_projectPath = '';

	/**
	 * Initializes the object.
	 */
	public function init()
	{
		$ds = DIRECTORY_SEPARATOR;
		$this->_projectPath = realpath( dirname( __FILE__ ) . $ds . '..' . $ds . '..' );

		require_once $this->_projectPath . $ds . 'Arcavias.php';
		spl_autoload_register( 'Arcavias::autoload' );

		$this->_arcavias = new Arcavias();

		$incPath = $this->_arcavias->getIncludePaths();
		$incPath[] = get_include_path();
		set_include_path( implode( PATH_SEPARATOR, $incPath ) );

		return true;
	}

	/**
	 * Generates JS package files given in the manifests, including extensions.
	 */
	public function main()
	{
		$this->log( 'Generating JSB2 packages' );

		foreach( $this->_arcavias->getCustomPaths( 'client/extjs' ) as $base => $paths )
		{
			foreach( $paths as $path )
			{
				$jsbPath = $base . DIRECTORY_SEPARATOR . $path;

				$this->log( sprintf( 'Package: %1$s ', $jsbPath ) );

				if( !is_file( $jsbPath ) || !is_readable( $jsbPath ) )
				{
					$this->log( sprintf( 'No manifest file found in %1$s', $jsbPath ) );
					continue;
				}

				try
				{
					$jsbParser = new MW_Jsb2_Default( $jsbPath );
					$jsbParser->deploy( 'js' );
				}
				catch( Exception $e )
				{
					$this->log( sprintf( 'Error: %1$s', $e->getMessage() ) );
				}
			}
		}
	}
}
