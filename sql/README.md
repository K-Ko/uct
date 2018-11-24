# Language code definitions

To refresh the definitions, download them and generate SQLs.

    wget -qO - https://www.iana.org/assignments/language-subtag-registry/language-subtag-registry | \

## Only the main 2-char languages

    php lang2sql.php 2 >code_lang_2.sql

## Full set, all languages

    php lang2sql.php >code_lang_all.sql
