<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


return array(
	'name' => 'aimeos-core',
	'depends' => array(),
	'include' => array(
		'lib/mshoplib/src',
		'lib/mwlib/src',
		'client/html/src',
		'controller/common/src',
		'controller/frontend/src',
		'controller/extjs/src',
		'controller/jobs/src',
		'lib/mwlib/lib',
	),
	'config' => array(
		'mysql' => array(
			'lib/mshoplib/config/common',
			'lib/mshoplib/config/mysql',
			'controller/frontend/config/controller',
			'controller/extjs/config/controller',
			'config',
		),
	),
	'i18n' => array(
		'client/extjs' => 'client/extjs/i18n',
		'client/extjs/ext' => 'client/extjs/i18n/ext',
		'client/html' => 'client/html/i18n',
		'client/html/code' => 'client/html/i18n/code',
		'client/html/country' => 'client/html/i18n/country',
		'client/html/currency' => 'client/html/i18n/currency',
		'client/html/language' => 'client/html/i18n/language',
		'controller/frontend' => 'controller/frontend/i18n',
		'mshop/code' => 'lib/mshoplib/i18n/code',
		'mshop' => 'lib/mshoplib/i18n',
	),
	'setup' => array(
		'lib/mshoplib/setup',
	),
	'custom' => array(
		'client/extjs' => array(
			'client/extjs/manifest.jsb2',
		),
		'client/html' => array(
			'client/html/layouts',
		),
		'controller/extjs' => array(
			'controller/extjs/src',
		),
		'controller/jobs' => array(
			'controller/jobs/src',
		),
		'controller/jobs/layouts' => array(
			'controller/jobs/layouts',
		),
	),
);
