DROP TABLE IF EXISTS `{{TABLE}}`;
CREATE TABLE `{{TABLE}}` (
  `set` varchar(16) NOT NULL DEFAULT '' COMMENT 'Code set',
  `lang` varchar(5) NOT NULL DEFAULT '' COMMENT 'Code language',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT 'Code',
  `desc` text NOT NULL COMMENT 'Translation text',
  `quantity` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Translation contains quantity-dependent text',
  `order` smallint(6) NOT NULL DEFAULT 0 COMMENT 'Sort order',
  `active` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '0: Deprecated / 1: Active',
  `hint` varchar(1024) NOT NULL DEFAULT '' COMMENT 'Hint for translator',
  PRIMARY KEY (`set`, `lang`, `code`),
  KEY `set_code` (`set`, `code`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Universal Code Translation';

-- Internals ----------------------------------------------------------------
REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_admin', '{{NATIVE}}', 'code_admin', '{"param":1,"slave":1}');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', '{{NATIVE}}', 'code_admin', 'Code admin', -1),
('code_set', '{{NATIVE}}', 'code_set', 'Code set', -1),

-- Languages ----------------------------------------------------------------
('code_set', 'en', 'code_lang', 'Language', -2),
('code_set', 'de', 'code_lang', 'Sprache', -2),
('code_set', 'fr', 'code_lang', 'La langue', -2),

('code_lang', 'en', 'en', 'English', 1),
('code_lang', 'en', 'de', 'German', 1),
('code_lang', 'en', 'fr', 'French', 1),

('code_lang', 'de', 'en', 'Englisch', 1),
('code_lang', 'de', 'de', 'Deutsch', 1),
('code_lang', 'de', 'fr', 'Französisch', 1),

('code_lang', 'fr', 'en', 'Anglais', 1),
('code_lang', 'fr', 'de', 'Allemand', 1),
('code_lang', 'fr', 'fr', 'Français', 1);

-- Native language first
UPDATE `{{TABLE}}`
   SET `order` = 0
 WHERE `set` = 'code_lang'
   AND `lang` = '{{NATIVE}}'
   AND `code` = '{{NATIVE}}';
