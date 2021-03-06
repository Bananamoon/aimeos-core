<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_product"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_product" (
				"siteid", "typeid", "code", "suppliercode", "label", "status",
				"start", "end", "config", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_product"
			SET "siteid" = ?, "typeid" = ?, "code" = ?, "suppliercode" = ?,
				"label" = ?, "status" = ?, "start" = ?, "end" = ?, "config" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mpro."id", mpro."siteid", mpro."typeid",
				mpro."label", mpro."status", mpro."start", mpro."end",
				mpro."code", mpro."suppliercode", mpro."config",
				mpro."ctime", mpro."mtime", mpro."editor"
			FROM "mshop_product" AS mpro
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mpro."id"
				FROM "mshop_product" AS mpro
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
