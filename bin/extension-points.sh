
#!/bin/bash
#
# Build extension point list
#

#set -x

root=$(dirname $(dirname $(readlink -f $0)))
tmp=$(mktemp)

trap 'rm $tmp' 0

{

echo '# Extension points'
echo
echo '| /app/tpl/... | Line | extension | '
echo '| ------------ | ---- | --------- | '

cat <<'EOT' >$tmp
<?php
while (!feof(STDIN)) {
    $line = fgets(STDIN);
    # echo $line, PHP_EOL;
    if (preg_match("~^(.*?):(\d+):.*\(('.*')\)~", $line, $match)) {
        # print_r($match);
        echo '| ', preg_replace("~^.*/app/tpl/~", "", $match[1]), ' | ', $match[2], ' | ', $match[3], ' | ', PHP_EOL;
    }
}
EOT

grep -Rin 'extensions.content' $root/app/tpl/* | php -f $tmp

} >$root/EXTENSION-POINTS.md
