--
-- MShop cache database definitions and descriptions
--
-- Copyright (c) Metaways Infosystems GmbH, 2014
-- License LGPLv3, http://opensource.org/licenses/LGPL-3.0
--


SET SESSION sql_mode='ANSI';



--
-- Cache table
--

CREATE TABLE "madmin_cache" (
	-- Unique id of the cache entry
	"id" VARCHAR(255) NOT NULL,
	-- site id
	"siteid" INTEGER NULL,
	-- Expiration time stamp
	"expire" DATETIME,
	-- Cached value
	"value" MEDIUMTEXT NOT NULL,
CONSTRAINT "pk_macac_id_siteid"
	PRIMARY KEY ("id", "siteid")
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_majob_expire" ON "madmin_cache" ("expire");


--
-- Cache tag table
--

CREATE TABLE "madmin_cache_tag" (
	-- Unique id of the cache entry
	"tid" VARCHAR(255) NOT NULL,
	-- site id
	"tsiteid" INTEGER NULL,
	-- Tag name
	"tname" VARCHAR(255) NOT NULL,
CONSTRAINT "unq_macacta_id_name"
	UNIQUE KEY ("tid", "tsiteid", "tname"),
CONSTRAINT "fk_macac_tid"
	FOREIGN KEY ("tid")
	REFERENCES "madmin_cache" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;
