-- Application settings --------------------------------------------------------
REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('code_admin', '{{NATIVE}}', 'code_editor_cfg', '{"param":1}');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'de', 'code_editor_cfg', 'Anwendungs-Einstellungen', -12),
('code_set', 'en', 'code_editor_cfg', 'Application settings', -12),
('code_set', 'fr', 'code_editor_cfg', 'Paramètres des applications', -12);

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `hint`) VALUES
('code_editor_cfg', '{{NATIVE}}', 'logo', '/icons/favicon-32x32.png', 'URL, scaled to 32x32 px'),
('code_editor_cfg', '{{NATIVE}}', 'auto_key_transform', '', 'Possible values if you wish: <span class="text-monospace">(camelcase|lowercase|uppercase)</span>');
