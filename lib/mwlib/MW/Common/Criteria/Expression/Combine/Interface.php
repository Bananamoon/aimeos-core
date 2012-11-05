<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Interface for combining objects.
 *
 * @package MW
 * @subpackage Common
 */
interface MW_Common_Criteria_Expression_Combine_Interface extends MW_Common_Criteria_Expression_Interface
{
	/**
	 * Returns the list of expressions that should be combined.
	 *
	 * @return array List of expressions
	 */
	public function getExpressions();
}
