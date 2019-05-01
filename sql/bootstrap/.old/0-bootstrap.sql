-- Users ----------------------------------------------------------------
REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_admin', '{{NATIVE}}', 'code_users', '{"param":1}');

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
