-- ---------------------------------------------------------------------------
-- Code set itself
-- ---------------------------------------------------------------------------
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', '{{NATIVE}}', 'code_set', 'Code set', -10);

-- ---------------------------------------------------------------------------
-- Users
-- ---------------------------------------------------------------------------
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_admin', '{{NATIVE}}', 'code_user', '{"param":1}');

INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'en', 'code_user', 'Users', -20),
('code_set', 'de', 'code_user', 'Benutzer', -20),
('code_set', 'fr', 'code_user', 'Utilisateurs', -20);

INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_editor', '{{NATIVE}}', '_code_user', 'SHA1(<password>)', 100);

-- ---------------------------------------------------------------------------
-- User access
-- ---------------------------------------------------------------------------
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_admin', '{{NATIVE}}', 'code_acl', '{"param":1}');

INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'en', 'code_acl', 'User rights', -21),
('code_set', 'de', 'code_acl', 'Benutzerrechte', -21),
('code_set', 'fr', 'code_acl', 'Droits de l\'utilisateur', -21);

INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_editor', 'en', '_code_acl', 'User level', 100),
('code_editor', 'de', '_code_acl', 'Benutzerstufe', 100),
('code_editor', 'fr', '_code_acl', 'Niveau utilisateur', 100);

-- ---------------------------------------------------------------------------
-- Languages
-- ---------------------------------------------------------------------------
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'en', 'code_lang', 'Language', -30),
('code_set', 'de', 'code_lang', 'Sprache', -30),
('code_set', 'fr', 'code_lang', 'La langue', -30);

-- Values
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_lang', 'en', 'en', 'English'),
('code_lang', 'en', 'de', 'German'),
('code_lang', 'en', 'fr', 'French'),

('code_lang', 'de', 'en', 'Englisch'),
('code_lang', 'de', 'de', 'Deutsch'),
('code_lang', 'de', 'fr', 'Französisch'),

('code_lang', 'fr', 'en', 'Anglais'),
('code_lang', 'fr', 'de', 'Allemand'),
('code_lang', 'fr', 'fr', 'Français');

-- Native language first
UPDATE `{{TABLE}}` SET `order` = 1
 WHERE `set` = 'code_lang' AND `lang` = '{{NATIVE}}' AND `code` != '{{NATIVE}}';

-- ---------------------------------------------------------------------------
-- Application texts
-- ---------------------------------------------------------------------------
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'en', 'code_editor_txt', 'Application texts', -40),
('code_set', 'de', 'code_editor_txt', 'Anwendungs-Texte', -40),
('code_set', 'fr', 'code_editor_txt', 'Textes d\'application', -40);

-- Values
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `hint`) VALUES
('code_editor_txt', '{{NATIVE}}', 'Logo', '/icons/favicon-32x32.png', 'Application logo, needs to be stored ONLY in "{{NATIVE}}"'),

('code_editor_txt', 'en', 'Description', 'Universal Code Translater', 'Header line'),
('code_editor_txt', 'en', 'Title', 'Universal Code Translater', 'Used for page title and navigation'),

('code_editor_txt', 'de', 'Description', 'Universal Code Übersetzer', 'Kopfzeile'),
('code_editor_txt', 'de', 'Title', 'Universal Code Übersetzer', 'Wird für Seitentitel und Navigation verwendet'),

('code_editor_txt', 'fr', 'Description', 'Traducteur de code universel', 'Ligne d\'en-tête'),
('code_editor_txt', 'fr', 'Title', 'Traducteur de code universel', 'Utilisé pour le titre de la page et la navigation');

-- ---------------------------------------------------------------------------
-- Code set code key format and regex
-- ---------------------------------------------------------------------------
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_admin', '{{NATIVE}}', 'code_format', '{"param":1}');

INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', '{{NATIVE}}', 'code_format', 'Code set format', -50);

INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_editor', 'en', '_code_format_hint', 'One of (uppercase|lowercase|camelcase)', 100),
('code_editor', 'de', '_code_format_hint', 'Eins von (uppercase|lowercase|camelcase)', 100),
('code_editor', 'fr', '_code_format_hint', 'Un de (uppercase|lowercase|camelcase)', 100);

-- ---------------------------------------------------------------------------
-- Code set code key Regex
-- ---------------------------------------------------------------------------
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_admin', '{{NATIVE}}', 'code_regex', '{"param":1}');

INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', '{{NATIVE}}', 'code_regex', 'Code set RegEx', -51);

-- Values
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_regex', '{{NATIVE}}', 'code_acl', '[A-Za-z][A-Za-z_.*]*');

INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_editor', '{{NATIVE}}', '_code_regex_hint', 'Code Regex', 100);

-- ---------------------------------------------------------------------------
-- Editor texts
-- ---------------------------------------------------------------------------
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_admin', '{{NATIVE}}', 'code_editor', '{"multi":1}');

-- Values
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'en', 'code_editor', 'Editor frontend texts', -90),
('code_set', 'de', 'code_editor', 'Editor Frontend-Texte', -90),
('code_set', 'fr', 'code_editor', 'Éditeur de textes de frontend', -90);

-- ---------------------------------------------------------------------------
-- Extensions
-- ---------------------------------------------------------------------------
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_admin', '{{NATIVE}}', 'code_ext', '{"multi":1}');

INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', '{{NATIVE}}', 'code_ext', 'Extensions texts', -100);
