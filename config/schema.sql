CREATE TABLE `model_history` (
  `id` char(36) NOT NULL,
  `model` varchar(255) DEFAULT NULL COMMENT 'e.g. "Installation"',
  `foreign_key` char(36) DEFAULT NULL COMMENT 'uuid',
  `user_id` char(36) DEFAULT NULL,
  `action` varchar(45) DEFAULT NULL COMMENT 'e.g. "create", "update", "delete"',
  `data` mediumblob COMMENT 'JSON blob, schema per action',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;