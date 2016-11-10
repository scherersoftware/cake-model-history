CREATE TABLE `model_history` (
  `id` char(36) NOT NULL,
  `model` varchar(255) DEFAULT NULL COMMENT 'e.g. "Installation"',
  `foreign_key` char(36) DEFAULT NULL COMMENT 'uuid',
  `user_id` char(36) DEFAULT NULL,
  `action` varchar(45) DEFAULT NULL COMMENT 'e.g. "create", "update", "delete"',
  `data` mediumblob COMMENT 'JSON blob, schema per action',
  `context` mediumblob COMMENT 'JSON blob, schema per action',
  `context_slug` varchar(255) DEFAULT NULL,
  `revision` int(8) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `data_search` (`model` ASC, `foreign_key` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
