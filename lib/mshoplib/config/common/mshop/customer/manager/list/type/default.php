<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_customer_list_type"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_customer_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_customer_list_type"
			SET "siteid"=?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mcuslity."id", mcuslity."siteid", mcuslity."code",
				mcuslity."domain", mcuslity."label", mcuslity."status",
				mcuslity."mtime", mcuslity."editor", mcuslity."ctime"
			FROM "mshop_customer_list_type" AS mcuslity
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcuslity."id"
				FROM "mshop_customer_list_type" as mcuslity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS LIST
		',
	),
);
