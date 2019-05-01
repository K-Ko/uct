# Password self service

Append menu entry to Administration menu for logged in users to change their password.

## Translations

Shortcut is `pss` for **P**assword **S**elf **S**ervice

### Common

Menu entry, Template header

    pss_ChangePassword

### Route

Alerts

    pss_PasswordChanged
    pss_PasswordsNotEqual

### Template

Form fields

    pss_ActualPassword
    pss_NewPassword
    pss_RepeatPassword

## Files

    # Extension class
    /app/extension/Pss.php

    # SQLs for texts
    /app/extension/Pss/install.sql
    /app/extension/Pss/uninstall.sql

    # Hook into administration menu
    /app/extension/Pss/nav-admin-after.twig

    # Password form template, must extend layout.twig
    /app/extension/Pss/tpl/pss.twig
