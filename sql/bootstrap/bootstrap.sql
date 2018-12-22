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
('code_set', '{{NATIVE}}', 'code_set', 'Code set', -1);

-- Languages ----------------------------------------------------------------
REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
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

-- Users ----------------------------------------------------------------
REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_admin', 'de', 'code_users', '{"param":1}');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`, `hint`) VALUES
('code_set', 'en', 'code_users', 'Users', -3, 'Password as [MD5 Hash](http://passwordsgenerator.net/sha1-hash-generator/)'),
('code_set', 'de', 'code_users', 'Benutzer', -3, 'Passwort als [MD5 Hash](http://passwordsgenerator.net/sha1-hash-generator/)'),
('code_set', 'en', 'code_users', 'Utilisateur', -3, 'Mot de passe comme [MD5 Hash](http://passwordsgenerator.net/sha1-hash-generator/)');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_users', 'en', 'admin', 'bf4223b9b44b7780f85eb79a8850434ebca5eaab', -1),
('code_users', 'de', 'admin', 'bf4223b9b44b7780f85eb79a8850434ebca5eaab', -1),
('code_users', 'fr', 'admin', 'bf4223b9b44b7780f85eb79a8850434ebca5eaab', -1);

-- ACL ----------------------------------------------------------------
REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_admin', '{{NATIVE}}', 'code_acl', '{"param":1}');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`, `hint`) VALUES
('code_set', 'en', 'code_acl', 'User code sets', -4, 'Format: `<code sets>(|<languages other than primary language>)`<br>(each comma separated and `*` for all)'),
('code_set', 'de', 'code_acl', 'Benutzer Code Sets', -4, 'Format: `<Code sets>(|<Sprachen außer Primärsprache>)`<br>(jeweils durch Komma getrennt und `*` für alle)'),
('code_set', 'en', 'code_acl', 'Ensembles de codes d\'utilisateur', -4, 'Format : `<jeux de codes>(|<langues autres que la langue maternelle>)`<br>(chaque virgule séparée et `*` pour toutes les virgules)');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_acl', '{{NATIVE}}', 'admin', '*', -1);
