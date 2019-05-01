DROP TABLE IF EXISTS `{{TABLE}}`;

CREATE TABLE `{{TABLE}}` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique record id',
    `app` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Application Id for multi app installations, 0 is the editor itself',
    `set` varchar(16) NOT NULL DEFAULT '' COMMENT 'Code set',
    `lang` varchar(5) NOT NULL DEFAULT '' COMMENT 'Code language',
    `code` varchar(50) NOT NULL DEFAULT '' COMMENT 'Code',
    `desc` text NOT NULL COMMENT 'Translation text',
    `quantity` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Translation contains quantity-dependent text',
    `var` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Description can contain formatting %? placeholder',
    `order` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Sort order',
    `active` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0: Deprecated / 1: Active',
    `hint` varchar(512) NOT NULL DEFAULT '' COMMENT 'Hint for translator',
    `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last changed',
    PRIMARY KEY (`id`),
    UNIQUE KEY `app` (`app`,`set`,`lang`,`code`),
    KEY `set_code` (`set`,`code`),
    KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=1 COMMENT='Universal Code Translation';

-- Native language and system app
INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES

('code_admin', '{{NATIVE}}', 'code_admin', '{"param":1,"slave":1}'),
('code_admin', '{{NATIVE}}', 'code_app', '{"param":1,"slave":1}'),
('code_app', '{{NATIVE}}', 0, '**SYSTEM**'),
('code_admin', '{{NATIVE}}', 'code_system', '["code_admin","code_set","code_lang","code_user","code_acl","code_format","code_regex","code_editor","code_editor_txt","code_ext"]');

INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', '{{NATIVE}}', 'code_admin', 'Code admin', -1);
