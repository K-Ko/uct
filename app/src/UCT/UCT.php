<?php
/**
 * UCT - Universal Code Translation
 *
 * @link       https://github.com/K-Ko/UCT
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2017 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

#
# Interface to a Universal Multilingual Code Table.
#
# Copyright (C) 2003 John Gorman <jgorman@webbysoft.com>
# http://www.webbysoft.com/babelkit
#

/**
 *
 */
namespace UCT;

/**
 *
 */
use RuntimeException;

/**
 *
 */
class UCT
{
    /**
     *
     */
    public $native;

    /**
     *
     */
    public function __construct(array $params)
    {
        if (empty($params['db'])) {
            throw new RuntimeException(__CLASS__ . ': Missing database parameter.');
        }

        $this->db    = $params['db'];
        $this->table = isset($params['table']) ? $params['table'] : 'uct';
        $this->app   = isset($params['app']) ? intval($params['app']) : 0;
        $this->cache = isset($params['cache']) ? $params['cache'] : null;

        // Find the native language
        $rows = $this->query($this->queries['native']);
        $this->native = isset($rows[0]['lang']) ? $rows[0]['lang'] : null;

        if (!$this->native) {
            throw new RuntimeException(
                __CLASS__ . ':Unable to determine native language. Check table ' .
                $this->table . ' for "code_admin / code_admin" record.'
            );
        }

        $this->active = $this->native;
    }

    /**
     * Check if a given language is defined and is active
     *
     * @param string $language Language code to check
     * @return integer -1 : Language does not exists
     *                  0 : Language exists but is inactive
     *                  1 : Language is active
     */
    public function checkLanguage($language)
    {
        $data = $this->get('lang', $this->native, $language);
        return $data ? $data['active'] : -1;
    }

    /**
     * Get a code description safe for html display.
     * Fill missing translations with the native desc or code.
     */
    public function desc($set, $lang, $code, $args = null)
    {
        return htmlspecialchars($this->render($set, $lang, $code, $args));
    }

    /**
     * Get a code description with the First letter capitalized.
     */
    public function ucfirst($set, $lang, $code, $args = null)
    {
        return ucfirst($this->desc($set, $lang, $code, $args));
    }

    /**
     * Get a code description with Each Word Capitalized.
     */
    public function ucwords($set, $lang, $code, $args = null)
    {
        return ucwords($this->desc($set, $lang, $code, $args));
    }

    /**
     * Get a raw code description *not* safe for html display.
     * Fill missing translations with the native desc or code.
     */
    public function render($set, $lang, $code, $args = null)
    {
        if (is_null($lang)) {
            $lang = $this->native;
        }

        $native = $this->get($set, $this->native, $code);
        $data   = $this->native != $lang ? $this->get($set, $lang, $code) : $native;

        if (is_null($data) && $lang != $this->native) {
            $data = $native;
        }

        if (is_null($data)) {
            return '';
        }

        $desc = $data['desc'];

        if ($desc == '') {
            $desc = $code;
        }

        if (!is_array($args) || count($args) == 0) {
            return $desc;
        }

        if ($native['quantity']) {
            $n = array();
            foreach (preg_split('~^---\s*$~m', $desc) as $d) {
                @list($k, $v) = explode('|', $d, 2);
                if ($v != '') {
                    $n[trim($k)] = trim($v);
                } else {
                    $n['*'] = trim($k);
                }
            }

            if (isset($n[$args[0]])) {
                $desc = $n[$args[0]];
            } elseif (isset($n['n'])) {
                $desc = $n['n'];
            } elseif (isset($n['*'])) {
                $desc = $n['*'];
            } else {
                throw new RuntimeException(
                    __CLASS__ . ':Missing entry "n" for '.$set.'::'.$code.'('.$lang.')'
                );
            }
        }

        return $native['var'] ? vsprintf($desc, $args) : $desc;
    }

    /**
     * Get a raw desc, *not* safe for html display
     *
     * Buffer request results
     */
    public function data($set, $lang, $code)
    {
        if ($code == '') {
            return '';
        }

        $result = $this->query($this->queries['desc'], $set, $lang, $code);

        return isset($result[0]['desc']) ? $result[0]['desc'] : '';
    }

    /**
     * Get a raw config parameter set (native).
     */
    public function paramSet($set)
    {
        return $this->query($this->queries['param-set'], $set);
    }

    /**
     * Get a raw config parameter (native).
     */
    public function param($set, $code)
    {
        return $this->data($set, $this->native, $code);
    }

    /**
     * Create an html form selection dropdown from a code set.
     */
    public function select($set, $lang, $params = [])
    {
        $params = array_merge(
            [ 'id' => '', 'var_name' => '', 'class' => '', 'options' => '' ],
            $params
        );

        extract($params);

        // Variable name.
        if (!$var_name) {
            $var_name = $set;
        }
        if (!$id) {
            $id = $set;
        }
        if ($class) {
            $class = ' class="' . $class - '"';
        }
        if ($options) {
            $options = ' ' . $options;
        }

        // Drop down box.
        return '<select id="'.$id.'" name="'.$var_name.'"'.$class.$options.'>' .
               $this->selectOptions($set, $lang, $params) .
               '</select>';
    }

    /**
     * Create the options for a html form selection dropdown from a code set.
     */
    public function selectOptions($set, $lang, $params = [])
    {
        $params = array_merge(
            [
                'value'         => null,
                'default'       => null,
                'subset'        => [],
                'exclude'       => [],
                'select_prompt' => null,
                'blank_prompt'  => null
            ],
            $params
        );

        extract($params);

        $options  = [];
        $selected = false;

        if (!isset($value)) {
            $value = $default;
        }

        // Blank options.
        if ($value == '') {
            if ($select_prompt == '') {
                $select_prompt = $this->ucwords('set', $lang, $set) . '?';
            }
            $options[] = '    <option value="" selected>' . $select_prompt . '</option>';
            $selected = true;
        } elseif ($blank_prompt <> '') {
            $options[] = '    <option value="">' . $blank_prompt . '</option>';
        }

        // Show code set options.
        $optgroup = false;
        $set_list = $this->fullSet($set, $lang);

        foreach ($set_list as $row) {
            if ($row['code'] == 'code_admin') {
                continue;
            }

            if (count($subset) && !in_array($row['code'], $subset) && $row['code'] <> $value ||
                in_array($row['code'], $exclude)) {
                continue;
            }

            $row['desc'] = htmlspecialchars($row['desc']);

            // ::optgoup label::
            if (preg_match('~^::+(.*?)::+$~', $row['desc'], $args)) {
                if ($optgroup) {
                    $options[] = '</optgroup>';
                }
                $options[] = '<optgroup label="' . $args['desc'] . '">';
                $optgroup = true;
            } else {
                if ($row['code'] == $value) {
                    $selected = true;
                    $options[] = '    <option value="' . $row['code'] . '" selected>' . $row['desc'] . '</option>';
                } elseif ($row['active']) {
                    $options[] = '    <option value="' . $row['code'] . '">' . $row['desc'] . '</option>';
                }
            }
        }
        if ($optgroup) {
            $options[] = '</optgroup>';
        }

        // Show a missing value.
        if (!$selected) {
            $options[] = '<option value="' . $value . '" selected>' . $value . '</option>';
        }

        return implode(PHP_EOL, $options);
    }

    /**
     * Create an html form radio box from a code set.
     */
    public function radio($set, $lang, $param = [])
    {
        $param = array_merge(
            [
                'var_name'      => null,
                'value'         => null,
                'default'       => null,
                'subset'        => null,
                'options'       => null,
                'blank_prompt'  => null,
                'sep'           => "<br>\n"
            ],
            $param
        );

        extract($param);

        // Variable name.
        if (!$var_name) {
            $var_name = $set;
        }

        if (!isset($value)) {
            $value = $default;
        }
        if (is_array($subset)) {
            $Subset = [];
            foreach ($subset as $val) {
                $Subset[$val] = 1;
            }
        }
        if ($options) {
            $options = " $options";
        }

        // Blank options.
        if ($value == '') {
            $selected = 1;
            if ($blank_prompt <> '') {
                $select .= "<input type='radio' name='$var_name'$options";
                $select .= " value='' checked>$blank_prompt";
            }
        } else {
            if ($blank_prompt <> '') {
                $select .= "<input type='radio' name='$var_name'$options";
                $select .= " value=''>$blank_prompt";
            }
        }

        // Show code set options.
        $set_list = $this->fullSet($set, $lang);

        foreach ($set_list as $row) {
            list($code, $desc) = $row;
            if ($Subset && !$Subset[$code] && $code <> $value) {
                continue;
            }
            $desc = htmlspecialchars(ucfirst($desc));
            if ($code == $value) {
                if ($select) {
                    $select .= $sep;
                }
                $selected = 1;
                $select .= "<input type='radio' name='$var_name'$options";
                $select .= " value='$code' checked>$desc";
            } elseif (!$row[3]) {
                if ($select) {
                    $select .= $sep;
                }
                $select .= "<input type='radio' name='$var_name'$options";
                $select .= " value='$code'>$desc";
            }
        }

        // Show missing values.
        if (!$selected) {
            if ($select) {
                $select .= $sep;
            }
            $select .= "<input type='radio' name='$var_name'$options";
            $select .= " value='$value' checked>$value";
        }

        return $select;
    }

    /**
     * Create an html form multiple select box from a code set.
     */
    public function multiple($set, $lang, $param = [])
    {
        $param = array_merge(
            [
                'id'            => $set,
                'var_name'      => $set,
                'value'         => null,
                'default'       => null,
                'subset'        => null,
                'options'       => null,
                'size'          => null
            ],
            $param
        );

        extract($param);

        if (!isset($value)) {
            $value = $default;
        }

        $values = [];
        if (is_array($value)) {
            foreach ($value as $val) {
                $values[$val] = 1;
            }
        } elseif ($value <> '') {
            $values[$value] = 1;
        }

        if (is_array($subset)) {
            $Subset = [];
            foreach ($subset as $val) {
                $Subset[$val] = 1;
            }
        }

        // Select multiple box.
        $select = "<select id='$id' name='$var_name"."[]'";
        if ($size) {
            $select .= " size='$size'";
        }
        $select .= " multiple $options>";

        // Show code set options.
        $set_list = $this->fullSet($set, $lang);
        foreach ($set_list as $row) {
            list($code, $desc) = $row;
            if ($Subset && !$Subset[$code] && !$values[$code]) {
                continue;
            }
            $desc = htmlspecialchars(ucfirst($desc));
            if ($values[$code]) {
                $select .= "<option value='$code' selected>$desc";
                unset($values[$code]);
            } elseif (!$row[3]) {
                $select .= "<option value='$code'>$desc";
            }
        }

        // Show missing values.
        foreach ($values as $code => $true) {
            $select .= "<option value='$code' selected>$code";
        }

        $select .= "</select>";

        return $select;
    }

    /**
     * Create an html form checkbox from a code set.
     */
    public function checkbox($set, $lang, $param = [])
    {
        $param = array_merge(
            [
                'var_name'      => $set,
                'value'         => null,
                'default'       => null,
                'subset'        => [],
                'exclude'       => [],
                'options'       => null,
                'sep'           => "<br>\n"
            ],
            $param
        );

        extract($param);

        if (!isset($value)) {
            $value = $default;
        }

        $values = [];
        if (is_array($value)) {
            foreach ($value as $val) {
                $values[$val] = true;
            }
        } elseif ($value <> '') {
            $values[$value] = true;
        }

        if (is_array($subset)) {
            $Subset = [];
            foreach ($subset as $val) {
                $Subset[$val] = 1;
            }
        }

        if ($options) {
            $options = " $options";
        }

        // Show code set options.
        $set_list = $this->fullSet($set, $lang);

        foreach ($set_list as $row) {
            list($code, $desc) = $row;
            if ($Subset && !$Subset[$code] && !$values[$code]) {
                continue;
            }
            $desc = htmlspecialchars(ucfirst($desc));
            if ($values[$code]) {
                if ($select) {
                    $select .= $sep;
                }
                $select .= "<input type='checkbox' name='$var_name"."[]'";
                $select .= "$options value='$code' checked>$desc";
                unset($values[$code]);
            } elseif (!$row[3]) {
                if ($select) {
                    $select .= $sep;
                }
                $select .= "<input type='checkbox' name='$var_name"."[]'";
                $select .= "$options value='$code'>$desc";
            }
        }

        // Show missing values.
        foreach ($values as $code => $true) {
            if ($select) {
                $select .= $sep;
            }
            $select .= "<input type='checkbox' name='$var_name"."[]'";
            $select .= "$options value='$code' checked>$code";
        }

        return $select;
    }

    /**
     * Get a language set array
     *
     * Buffer request results
     */
    public function languageSet($set, $lang = null)
    {
        return $this->query($this->queries['language-set'], $set, $lang ?: $this->native, $this->native);
    }

    /**
     * Get a full language set with missing translations in native.
     */
    public function fullSet($set, $lang)
    {
        $data = $this->languageSet($set, $this->native);

        if ($lang != $this->native) {
            $other  = $this->languageSet($set, $lang);
            $lookup = [];

            foreach ($other as $row) {
                $lookup[$row['code']] = $row['desc'];
            }

            foreach ($data as $ord => $row) {
                if (isset($lookup[$row['code']])) {
                    $data[$ord]['desc'] = $lookup[$row['code']];
                }
            }
        }

        return $data;
    }

    /**
     * Get a language set array
     *
     * Buffer request results
     */
    public function fullSetAssoc($set, $lang)
    {
        $data = [];

        foreach ($this->fullSet($set, $lang) as $row) {
            $data[$row['code']] = $row;
        }

        return $data;
    }

    /**
     * Get code desc, order, and active flag
     */
    public function get($set, $lang, $code)
    {
        if ($code == '') {
            return null;
        }

        $result = $this->query($this->queries['get'], $set, $lang, $code);

        return isset($result[0]) ? $result[0] : null;
    }

    /**
     *
     */
    public function sql($query)
    {
        // Replace common placeholders
        $query = str_replace('{{TABLE}}', $this->table, $query);
        $query = str_replace('{{NATIVE}}', $this->native, $query);
        $query = str_replace('{{APP}}', $this->app, $query);

        return $query;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $db;

    /**
     *
     */
    protected $table;

    /**
     *
     */
    protected $app = 0;

    /**
     *
     */
    protected $cache;

    /**
     *
     */
    protected $queries = [
        'native' =>
            'SELECT `lang`
               FROM `{{TABLE}}`
              WHERE `app` = {{APP}}
                AND `set` = "code_admin"
                AND `code` = "code_admin"',

        'desc' =>
            'SELECT `desc`
               FROM `{{TABLE}}`
              WHERE `app` = {{APP}} AND `set` = "%s" AND `lang` = "%s" AND `code` = "%s"
              LIMIT 1',

        'get' =>
            'SELECT `desc`, `quantity`, `var`, `order`, `active`, `hint`
               FROM `{{TABLE}}`
              WHERE `app` = {{APP}} AND `set` = "%s" AND `lang` = "%s" AND `code` = "%s"
              LIMIT 1',

        'language-set' =>
            // Order by native language order!
            'SELECT l.`code`, l.`desc`, l.`quantity`, l.`var`, n.`order`, n.`active`, l.`hint`
               FROM `{{TABLE}}` AS l
               JOIN `{{TABLE}}` AS n USING (`app`, `set`, `code`)
              WHERE l.`app`  = {{APP}}
                AND l.`set`  = "%s"
                AND l.`lang` = "%s"
                AND n.`lang` = "%s"
              GROUP BY l.`code`, l.`desc`
              ORDER BY n.`order`, n.`code`',

        'param-set' =>
            'SELECT `code`, `desc`, `active`, `hint`, `changed`
            FROM `{{TABLE}}`
            WHERE `app` = {{APP}} AND `set` = "%s" AND `lang` = "{{NATIVE}}"
            ORDER BY `code`', // `order` is always 0 for parameter sets
    ];

    /**
     *
     */
    protected function query($query)
    {
        $args = func_get_args();

        // Get query
        $query = array_shift($args);
        $query = $this->sql($query);

        // Quote arguments!
        foreach ($args as &$value) {
            $value = $this->db->real_escape_string($value);
        }

        // Replace remaining arguments
        $query = vsprintf($query, $args);

        $result = $this->db->query($query);

        if ($result) {
            if (is_scalar($result)) {
                return $result;
            }
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        } else {
            $data = null;
        }

        return $data;
    }
}
