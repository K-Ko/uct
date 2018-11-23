# Language code definitions

To refresh the definitions, download them and generate SQLs.

## Only the main 2-char languages

    wget -4qO - https://www.iana.org/assignments/language-subtag-registry/language-subtag-registry | \
    php lang2sql.php 2 >code_lang_2.sql

## Full set, all languages

    wget -4qO - https://www.iana.org/assignments/language-subtag-registry/language-subtag-registry | \
    php lang2sql.php >code_lang_all.sql
