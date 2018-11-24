DROP TABLE IF EXISTS `{{TABLE}}`;

CREATE TABLE `{{TABLE}}` (
  `set` varchar(16) NOT NULL DEFAULT '' COMMENT 'Code set',
  `lang` varchar(5) NOT NULL DEFAULT '' COMMENT 'Code language',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT 'Code',
  `desc` text NOT NULL COMMENT 'Translation text',
  `quantity` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Translation contains quantity-dependent text',
  `order` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Sort order',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0: Deprecated / 1: Active',
  `hint` varchar(1024) NOT NULL DEFAULT '' COMMENT 'Hint for translator',
  PRIMARY KEY (`set`,`lang`,`code`),
  KEY `set_code` (`set`,`code`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Universal Code Translation';

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `quantity`, `order`, `active`) VALUES

('code_admin', '{{PRIMARY}}', 'code_admin', 'param=1 slave=1', 0, 0, 1),

('code_set', '{{PRIMARY}}', 'code_admin', 'Code admin', 0, -1, 1),

('code_set', '{{PRIMARY}}', 'code_set', 'Code set', 0, -3, 1),

-- Languages ----------------------------------------------------------------
('code_set', 'en', 'code_lang', 'Language', 0, -2, 1),
('code_set', 'de', 'code_lang', 'Sprache', 0, -2, 1),
('code_set', 'fr', 'code_lang', 'La langue', 0, -2, 1),

('code_lang', 'en', 'de', 'German', 0, 1, 0),
('code_lang', 'en', 'en', 'English', 0, 1, 0),
('code_lang', 'en', 'fr', 'French', 0, 1, 0),
('code_lang', 'de', 'de', 'Deutsch', 0, 1, 0),
('code_lang', 'de', 'en', 'Englisch', 0, 1, 0),
('code_lang', 'de', 'fr', 'Französisch', 0, 1, 0),
('code_lang', 'fr', 'de', 'Allemand', 0, 1, 0),
('code_lang', 'fr', 'en', 'Anglais', 0, 1, 0),
('code_lang', 'fr', 'fr', 'Français', 0, 1, 0);

-- Primary language
UPDATE `{{TABLE}}`
   SET `order` = 0
 WHERE `set` = 'code_lang'
   AND `lang` = '{{PRIMARY}}'
   AND `code` = '{{PRIMARY}}';
UPDATE `{{TABLE}}`
   SET `active` = 1
 WHERE `set` = 'code_lang'
   AND `lang` = '{{PRIMARY}}';


-- Editor configuration -----------------------------------------------------
REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `quantity`, `order`, `active`) VALUES

('code_admin', '{{PRIMARY}}', 'code_editor_cfg', '', 0, 0, 1),

('code_set', 'de', 'code_editor_cfg', 'Anwendungseinstellungen', 0, 0, 0),
('code_set', 'en', 'code_editor_cfg', 'Application settings', 0, 0, 0),
('code_set', 'fr', 'code_editor_cfg', 'Paramètres de l\'application', 0, 0, 0),
-- Overwrite for primary language
('code_set', '{{PRIMARY}}', 'code_editor_cfg', 'Application settings', 0, -1, 1);

-- Editor configuration -----------------------------------------------------
REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `quantity`, `order`, `active`, `hint`) VALUES
('code_editor_cfg', 'de', 'Title', 'Universal Code Übersetzer', 0, 0, 0, 'Wird für Browser-Tab und Frontend verwendet'),
('code_editor_cfg', 'en', 'Title', 'Universal Code Translater', 0, 0, 0, 'Used for browser tab and frontend'),
('code_editor_cfg', 'fr', 'Title', 'Traducteur de code universel', 0, 0, 0, 'Utilisé pour l\'onglet et le frontal du navigateur');
UPDATE `{{TABLE}}`
   SET `active` = 1
 WHERE `set` = 'code_editor_cfg'
   AND `lang` = '{{PRIMARY}}'
   AND `code` = 'Title';

-- Editor texts -------------------------------------------------------------
REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `quantity`, `order`, `active`) VALUES

('code_admin', '{{PRIMARY}}', 'code_editor', 'multi=1', 0, 0, 1),

('code_set', 'en', 'code_editor', 'UCT editor', 0, 0, 0),
('code_set', 'de', 'code_editor', 'UCT editor', 0, 0, 0),
('code_set', 'fr', 'code_editor', 'UCT éditeur', 0, 0, 0),
-- Overwrite for primary language
('code_set', '{{PRIMARY}}', 'code_editor', 'UCT editor', 0, -1, 1),

('code_editor', 'de', 'Add', 'Neu', 0, 0, 1),
('code_editor', 'de', 'AddCode', 'Neuen Code hinzufügen', 0, 0, 1),
('code_editor', 'de', 'Administration', 'Administration', 0, 0, 1),
('code_editor', 'de', 'AllCodes', 'Alle Code Sets', 0, 0, 1),
('code_editor', 'de', 'AreYouSure', 'Sind Sie sicher?', 0, 0, 1),
('code_editor', 'de', 'Code', 'Code', 0, 0, 1),
('code_editor', 'de', 'CodeDeleted', 'Code gelöscht', 0, 0, 1),
('code_editor', 'de', 'CodeMaxChars', 'max. %d Zeichen', 0, 0, 1),
('code_editor', 'de', 'CodeOrder', 'Reihenfolge', 0, 0, 1),
('code_editor', 'de', 'CodeOrderIncrement', 'Schrittweite', 0, 0, 1),
('code_editor', 'de', 'CodeOrderNotNumeric', 'Code-Reihenfolge ist nicht numerisch', 0, 0, 1),
('code_editor', 'de', 'CodeRegexFailed', 'Code muss folgendem Muster entsprechen: `[a-zA-Z0-9_]+`', 0, 0, 1),
('code_editor', 'de', 'CodeSaved', 'Code gesichert', 0, 0, 1),
('code_editor', 'de', 'CodeStateToggled', 'Code Status umgeschaltet', 0, 0, 0),
('code_editor', 'de', 'CreateSlaveSet', 'Slave-Set anlegen', 0, 0, 1),
('code_editor', 'de', 'Delete', 'Löschen', 0, 0, 1),
('code_editor', 'de', 'Deprecated', 'Veraltet', 0, 0, 1),
('code_editor', 'de', 'DeprecatedHint', 'Code ist veraltet, wird angezeigt wie zuvor, ist aber nicht für zukünftige Verwendung auswählbar', 0, 0, 1),
('code_editor', 'de', 'Description', 'Beschreibung', 0, 0, 1),
('code_editor', 'de', 'DisplayLanguage', 'Anzeige', 0, 0, 1),
('code_editor', 'de', 'Edit', 'Bearbeiten', 0, 0, 1),
('code_editor', 'de', 'GoogleTranslate', 'Google Übersetzer', 0, 0, 1),
('code_editor', 'de', 'Hint', 'Hinweis', 0, 0, 1),
('code_editor', 'de', 'InvalidCode', 'Ungültiger Code', 0, 0, 1),
('code_editor', 'de', 'LoggedIn', 'Eingeloggt', 0, 0, 1),
('code_editor', 'de', 'LoggedOut', 'Ausgeloggt', 0, 0, 1),
('code_editor', 'de', 'MainTranslationPage', 'Startseite', 0, 0, 1),
('code_editor', 'de', 'MultilineSet', 'Multiline-Set', 0, 0, 1),
('code_editor', 'de', 'MultilineSetHint', 'Langtext-Modus', 0, 0, 1),
('code_editor', 'de', 'NeedsTranslation', '%d Sprachensätze (%d Einträge) brauchen Übersetzungsarbeit', 0, 0, 1),
('code_editor', 'de', 'New', 'Neu', 0, 0, 1),
('code_editor', 'de', 'No', 'Nein', 0, 0, 1),
('code_editor', 'de', 'none', 'keine', 0, 0, 1),
('code_editor', 'de', 'NormalSet', 'Normales Set', 0, 0, 1),
('code_editor', 'de', 'NumberOfMany', '#%d von %d', 0, 0, 1),
('code_editor', 'de', 'Other', 'Andere', 0, 0, 1),
('code_editor', 'de', 'ParameterSet', 'Parameter-Set', 0, 0, 1),
('code_editor', 'de', 'ParameterSetHint', 'Parameter-Sets werden nicht übersetzt', 0, 0, 1),
('code_editor', 'de', 'RecordAdded', 'Datensatz hinzugefügt', 0, 0, 1),
('code_editor', 'de', 'Records', '0 | Keine Einträge\n---\n1 | 1 Eintrag\n---\nn | %d Einträge', 1, 0, 1),
('code_editor', 'de', 'RecordUpdated', 'Datensatz geändert', 0, 0, 1),
('code_editor', 'de', 'Save', 'Sichern', 0, 0, 1),
('code_editor', 'de', 'SelectSetLanguage', 'Wähle ein Code Set und bis zu 2 Sprachen aus', 0, 0, 1),
('code_editor', 'de', 'Skip', 'Überspringen', 0, 0, 1),
('code_editor', 'de', 'SlaveSet', 'Slave-Set', 0, 0, 1),
('code_editor', 'de', 'SlaveSetData', 'Set Daten', 0, 0, 1),
('code_editor', 'de', 'SlaveSetDataHint', 'Ein Eintrag pro Zeile', 0, 0, 1),
('code_editor', 'de', 'SlaveSetHint', 'Slave-Sets werden nur übersetzt', 0, 0, 1),
('code_editor', 'de', 'ToggleState', 'Status des Codes umschalten', 0, 0, 0),
('code_editor', 'de', 'TranslationSets', 'Übersetzungssets', 0, 0, 1),
('code_editor', 'de', 'ViewSet', 'Set anzeigen', 0, 0, 1),
('code_editor', 'de', 'WithQuantity', 'Mengenabhängig', 0, 0, 0),
('code_editor', 'de', 'WithQuantityHint', 'Enthält mengenabhängigen Text', 0, 0, 0),
('code_editor', 'de', 'Yes', 'Ja', 0, 0, 1),
('code_editor', 'en', 'Add', 'Add', 0, 0, 1),
('code_editor', 'en', 'AddCode', 'Add new code', 0, 0, 1),
('code_editor', 'en', 'Administration', 'Administration', 0, 0, 1),
('code_editor', 'en', 'AllCodes', 'All code sets', 0, 0, 1),
('code_editor', 'en', 'AreYouSure', 'Are you sure?', 0, 0, 1),
('code_editor', 'en', 'Code', 'Code', 0, 0, 1),
('code_editor', 'en', 'CodeDeleted', 'Code deleted', 0, 0, 1),
('code_editor', 'en', 'CodeMaxChars', 'max. %d characters', 0, 0, 1),
('code_editor', 'en', 'CodeOrder', 'Code order', 0, 0, 1),
('code_editor', 'en', 'CodeOrderIncrement', 'Order increment', 0, 0, 1),
('code_editor', 'en', 'CodeOrderNotNumeric', 'Code order is not numeric', 0, 0, 1),
('code_editor', 'en', 'CodeRegexFailed', 'Code must match the pattern `[a-zA-Z0-9_]+`', 0, 0, 1),
('code_editor', 'en', 'CodeSaved', 'Code saved', 0, 0, 1),
('code_editor', 'en', 'CodeStateToggled', 'Code state toggled', 0, 0, 1),
('code_editor', 'en', 'CreateSlaveSet', 'Create slave set', 0, 0, 1),
('code_editor', 'en', 'Delete', 'Delete', 0, 0, 1),
('code_editor', 'en', 'Deprecated', 'Deprecated', 0, 0, 1),
('code_editor', 'en', 'DeprecatedHint', 'Code is deprecated, will be displayed as before but not selectable for fututre use', 0, 0, 1),
('code_editor', 'en', 'Description', 'Description', 0, 0, 1),
('code_editor', 'en', 'DisplayLanguage', 'Display', 0, 0, 1),
('code_editor', 'en', 'Edit', 'Edit', 0, 0, 1),
('code_editor', 'en', 'GoogleTranslate', 'Google Translate', 0, 0, 1),
('code_editor', 'en', 'Hint', 'Notes', 0, 0, 1),
('code_editor', 'en', 'InvalidCode', 'Invalid code', 0, 0, 1),
('code_editor', 'en', 'LoggedIn', 'Logged in', 0, 0, 1),
('code_editor', 'en', 'LoggedOut', 'Logged out', 0, 0, 1),
('code_editor', 'en', 'MainTranslationPage', 'Homepage', 0, 0, 1),
('code_editor', 'en', 'MultilineSet', 'Multiline set', 0, 0, 1),
('code_editor', 'en', 'MultilineSetHint', 'Paragraph mode', 0, 0, 1),
('code_editor', 'en', 'NeedsTranslation', '%d language sets (%d codes) need translation work', 0, 0, 1),
('code_editor', 'en', 'New', 'New', 0, 0, 1),
('code_editor', 'en', 'No', 'No', 0, 0, 1),
('code_editor', 'en', 'none', 'none', 0, 0, 1),
('code_editor', 'en', 'NormalSet', 'Normal set', 0, 0, 1),
('code_editor', 'en', 'NumberOfMany', '#%d of %d', 0, 0, 1),
('code_editor', 'en', 'Other', 'Other', 0, 0, 1),
('code_editor', 'en', 'ParameterSet', 'Parameter set', 0, 0, 1),
('code_editor', 'en', 'ParameterSetHint', 'Parameter sets are not translated', 0, 0, 1),
('code_editor', 'en', 'RecordAdded', 'Record added', 0, 0, 1),
('code_editor', 'en', 'Records', '0 | No records\n---\n1 | 1 record\n---\nn | %d records', 1, 0, 1),
('code_editor', 'en', 'RecordUpdated', 'Record updated', 0, 0, 1),
('code_editor', 'en', 'Save', 'Save', 0, 0, 1),
('code_editor', 'en', 'SelectSetLanguage', 'Select a code set and up to 2 languages', 0, 0, 1),
('code_editor', 'en', 'Skip', 'Skip', 0, 0, 1),
('code_editor', 'en', 'SlaveSet', 'Slave set', 0, 0, 1),
('code_editor', 'en', 'SlaveSetData', 'Set data', 0, 0, 1),
('code_editor', 'en', 'SlaveSetDataHint', 'One entry per line', 0, 0, 1),
('code_editor', 'en', 'SlaveSetHint', 'Slave sets are for translation only', 0, 0, 1),
('code_editor', 'en', 'ToggleState', 'Toggle code state', 0, 0, 1),
('code_editor', 'en', 'TranslationSets', 'Translation sets', 0, 0, 1),
('code_editor', 'en', 'ViewSet', 'View Set', 0, 0, 1),
('code_editor', 'en', 'WithQuantity', 'Quantity-dependent', 0, 0, 1),
('code_editor', 'en', 'WithQuantityHint', 'Contains quantity-dependent content', 0, 0, 1),
('code_editor', 'en', 'Yes', 'Yes', 0, 0, 1),
('code_editor', 'fr', 'Add', 'Ajouter', 0, 0, 1),
('code_editor', 'fr', 'AddCode', 'Ajouter un nouveau code', 0, 0, 1),
('code_editor', 'fr', 'Administration', 'Administration', 0, 0, 1),
('code_editor', 'fr', 'AllCodes', 'Tous les jeux de codes', 0, 0, 1),
('code_editor', 'fr', 'AreYouSure', 'Êtes-vous sûr?', 0, 0, 1),
('code_editor', 'fr', 'Code', 'Code', 0, 0, 1),
('code_editor', 'fr', 'CodeDeleted', 'Code supprimé', 0, 0, 1),
('code_editor', 'fr', 'CodeMaxChars', 'max. %d caractères', 0, 0, 1),
('code_editor', 'fr', 'CodeOrder', 'Commande de code', 0, 0, 1),
('code_editor', 'fr', 'CodeOrderIncrement', 'Incrément', 0, 0, 1),
('code_editor', 'fr', 'CodeOrderNotNumeric', 'L\'ordre du code n\'est pas numérique', 0, 0, 1),
('code_editor', 'fr', 'CodeRegexFailed', 'Le code doit correspondre au modèle `[a-zA-Z0-9_]+`', 0, 0, 1),
('code_editor', 'fr', 'CodeSaved', 'Code enregistré', 0, 0, 1),
('code_editor', 'fr', 'CodeStateToggled', 'État du code basculé', 0, 0, 0),
('code_editor', 'fr', 'CreateSlaveSet', 'Créer un ensemble d\'esclaves', 0, 0, 1),
('code_editor', 'fr', 'Delete', 'Effacer', 0, 0, 1),
('code_editor', 'fr', 'Deprecated', 'Déprécié', 0, 0, 1),
('code_editor', 'fr', 'DeprecatedHint', 'Le code est obsolète, sera affiché comme avant mais ne peut pas être sélectionné pour une utilisation future', 0, 0, 1),
('code_editor', 'fr', 'Description', 'La description', 0, 0, 1),
('code_editor', 'fr', 'DisplayLanguage', 'Afficher', 0, 0, 1),
('code_editor', 'fr', 'Edit', 'Modifier', 0, 0, 1),
('code_editor', 'fr', 'GoogleTranslate', 'Google Traduction', 0, 0, 1),
('code_editor', 'fr', 'Hint', 'Remarques', 0, 0, 1),
('code_editor', 'fr', 'InvalidCode', 'Code invalide', 0, 0, 1),
('code_editor', 'fr', 'LoggedIn', 'Connecté', 0, 0, 1),
('code_editor', 'fr', 'LoggedOut', 'Déconnecté', 0, 0, 1),
('code_editor', 'fr', 'MainTranslationPage', 'Homepage', 0, 0, 1),
('code_editor', 'fr', 'MultilineSet', 'Jeu multiligne', 0, 0, 1),
('code_editor', 'fr', 'MultilineSetHint', 'Mode de paragraphe', 0, 0, 1),
('code_editor', 'fr', 'NeedsTranslation', '%d jeux de langues (%d codes) ont besoin d\'un travail de traduction', 0, 0, 1),
('code_editor', 'fr', 'New', 'Nouveau', 0, 0, 1),
('code_editor', 'fr', 'No', 'Non', 0, 0, 1),
('code_editor', 'fr', 'none', 'aucun', 0, 0, 1),
('code_editor', 'fr', 'NormalSet', 'Jeu normal', 0, 0, 1),
('code_editor', 'fr', 'NumberOfMany', '#%d de %d', 0, 0, 1),
('code_editor', 'fr', 'Other', 'Autre', 0, 0, 1),
('code_editor', 'fr', 'ParameterSet', 'Paramètre', 0, 0, 1),
('code_editor', 'fr', 'ParameterSetHint', 'Les jeux de paramètres ne sont pas traduits', 0, 0, 1),
('code_editor', 'fr', 'RecordAdded', 'Enregistrement ajouté', 0, 0, 1),
('code_editor', 'fr', 'Records', '0 | Aucun enregistrement\n---\n1 | 1 enregistrement\n---\nn | %d enregistrements', 1, 0, 1),
('code_editor', 'fr', 'RecordUpdated', 'Données mises à jour', 0, 0, 1),
('code_editor', 'fr', 'Save', 'Enregistrer', 0, 0, 1),
('code_editor', 'fr', 'SelectSetLanguage', 'Sélectionnez un jeu de codes et jusqu\'à 2 langues', 0, 0, 1),
('code_editor', 'fr', 'Skip', 'Sauter', 0, 0, 1),
('code_editor', 'fr', 'SlaveSet', 'Esclave', 0, 0, 1),
('code_editor', 'fr', 'SlaveSetData', 'Définir les données', 0, 0, 1),
('code_editor', 'fr', 'SlaveSetDataHint', 'Une entrée par ligne', 0, 0, 1),
('code_editor', 'fr', 'SlaveSetHint', 'Les ensembles d\'esclaves sont pour la traduction seulement', 0, 0, 1),
('code_editor', 'fr', 'ToggleState', 'Basculer l\'état du code', 0, 0, 0),
('code_editor', 'fr', 'TranslationSets', 'Ensembles de traduction', 0, 0, 1),
('code_editor', 'fr', 'ViewSet', 'Voir l\'ensemble', 0, 0, 1),
('code_editor', 'fr', 'WithQuantity', 'Quantité dépendante', 0, 0, 0),
('code_editor', 'fr', 'WithQuantityHint', 'Contient un contenu dépendant de la quantité', 0, 0, 0),
('code_editor', 'fr', 'Yes', 'Oui', 0, 0, 1);
