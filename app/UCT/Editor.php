<?php
/**
 * UCT - Universal Code Translation
 *
 * @link       https://github.com/K-Ko/UCT
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2017 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 *
 */
namespace UCT;

/**
 *
 */
use Exception;

/**
 *
 */
class Editor extends UCT
{
    public function __call($name, $args)
    {
        $trace = debug_backtrace(0, 2);

        die(
            '<pre>Invalid call: ' . $trace[1]['class'] . $trace[1]['type'] . $trace[0]['args'][0] . '() ' .
            'in ' . $trace[1]['class'] . $trace[1]['type'] . $trace[1]['function'] . '() ' .
            'in ' . $trace[0]['file'] . '(' . $trace[0]['line'] . ')'
        );
    }

    /**
     *
     */
    public function __construct(array $params)
    {
        // Extend queries before parent::__construct()
        $this->queries = array_merge($this->queries, $this->queriesEditor);
        parent::__construct($params);
    }

    /**
     * Put a code. Insert, update or delete as appropriate.
     */
    public function putData($set, $lang, $code, $desc, $quantity = 0, $order = 0, $active = 1)
    {
        // Get the existing code info, if any.
        $old = $this->get($set, $lang, $code);

        // Field work.
        if ($lang != $this->native) {
            $quantity = 0;
            $order    = 0;
            $active   = 0;
        }

        $desc = trim(str_replace(["\r\n", "\r"], "\n", $desc));
        // Quantity strings
        $desc = str_replace('%', '%%', $desc);

        // add, update, or delete
        if ($old) {
            if ($desc <> '') {
                if ($desc <> $old['desc'] || $order <> $old['order'] || $active <> $old['active']) {
                    $sql = $this->sql(
                        $this->queries['update'],
                        $this->db->real_escape_string($desc),
                        $quantity,
                        $order,
                        $active,
                        $set,
                        $lang,
                        $code
                    );
                    $this->query($sql);
                }
            } else {
                if ($lang == $this->native) {
                    $this->remove($set, $code);
                } else {
                    $sql = $this->sql(
                        $this->queries['delete'],
                        $set,
                        $lang,
                        $code
                    );
                    $this->query($sql);
                }
            }
        } elseif ($desc <> '') {
            $sql = $this->sql(
                $this->queries['insert'],
                $set,
                $lang,
                $code,
                $this->db->real_escape_string($desc),
                $quantity,
                $order,
                $active
            );
            $this->query($sql);
        }
    }

    /**
     * Toggle a code, set active 0|1.
     */
    public function toggleActive($set, $code)
    {
        $this->query(
            $this->sql($this->queries['toggle'], $set, $this->native, $code)
        );
    }

    /**
     * Add or update a slave code native description.
     */
    public function putSlave($set, $code, $desc)
    {
        $old = $this->get($set, $this->native, $code);

        if ($old) {
            // Update
            if ($desc <> $old['desc']) {
                $this->put(
                    $set,
                    $this->native,
                    $code,
                    $desc,
                    $old['quantity'],
                    $old['order'],
                    $old['active']
                );
            }
        } else {
            // Insert
            $this->put($set, $this->native, $code, $desc);
        }
    }

    /**
     * Remove a code completely.
     */
    public function removeData($set, $code)
    {
        $this->query($this->queries['remove'][0], $set, $code);

        // Delete whole set?
        if ($set == 'code_set') {
            // Remove code_admin entry
            $this->query($this->queries['remove'][1], $code);
            // Remove remaining codes
            $this->query($this->queries['remove'][2], $code);
        }
    }

    /**
     *
     */
    public function adminGet($set)
    {
        $admin = array('param' => '', 'slave' => '', 'multi' => '');

        if ($params = $this->param('code_admin', $set)) {
            $params = explode(' ', $params);
            foreach ($params as $n => $param) {
                if (preg_match('~^(.+?)=(.*)$~', $param, $args)) {
                    $admin[$args[1]] = $args[2];
                }
            }
        }

        return $admin;
    }

    /**
     *
     */
    public function adminPut($set, $admin = [])
    {
        $params = [];

        foreach (['param', 'slave', 'multi'] as $attr) {
            if (array_key_exists($attr, $admin) && $admin[$attr]) {
                $params[] = $attr . '=' . $admin[$attr];
            }
        }

        $this->putData('code_admin', $this->native, $set, implode(' ', $params));
    }

    /**
     * Find a code's position in the set.
     */
    public function getNav($set, $code)
    {
        $set   = $this->languageSet($set);
        $count = count($set);

        if ($count) {
            $first = $set[0]['code'];
            $last  = $set[$count - 1]['code'];
        } else {
            $first = $last = '';
        }

        for ($n = 0; $n < $count; $n++) {
            if ($set[$n]['code'] == $code) {
                break;
            }
        }

        if ($n == $count) {
            // NOT found, invalid code!
            return false;
        }

        if ($n == 0) {
            $prev = $last;
            if ($count > 1) {
                $next = $set[$n + 1]['code'];
            } else {
                $next = $last;
            }
        } elseif ($n == $count - 1) {
            $prev = $set[$n - 1]['code'];
            $next = $first;
        } else {
            $prev = $set[$n - 1]['code'];
            $next = $set[$n + 1]['code'];
        }

        return [
            'first' => $first, 'prev' => $prev, 'next' => $next,
            'last' => $last, 'pos' => $n + 1, 'count' => $count
        ];
    }

    /**
     * Get a language set array
     *
     * Buffer request results
     */
    public function activeLanguages()
    {
        return count($this->activeLanguageSets());
    }

    /**
     * Get a language set array
     *
     * Buffer request results
     */
    public function activeLanguageSets()
    {
        $sql = $this->sql($this->queries['active_language_sets']);

        $sets = [];
        foreach ($this->query($sql) as $set) {
            $sets[] = $set['code'];
        }
        return $sets;
    }

    /**
     * Get the hint for a code
     */
    public function getHint($set, $code)
    {
        $result = $this->query($this->queries['get-hint'], $set, $this->native, $code);
        return isset($result[0]['hint']) ? $result[0]['hint'] : '';
    }

    /**
     * Set the hint for a code
     */
    public function setHint($set, $code, $hint)
    {
        $sql = $this->sql($this->queries['set-hint'], $hint, $set, $this->native, $code);
        $this->query($sql);
    }

    /**
     * Get the code counts for all language sets
     */
    public function getCount()
    {
        $counts = [];
        foreach ($this->query($this->queries['count']) as $row) {
            $counts[$row['set']][$row['lang']] = $row['count'];
        }
        return $counts;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    protected $queriesEditor = [
        'get-hint' =>
            'SELECT `hint`
               FROM `{TABLE}`
              WHERE `set` = "%s" AND `lang` = "%s" AND `code` = "%s"
              LIMIT 1',

        'set-hint' =>
            'UPDATE `{TABLE}`
                SET `hint` = "%s"
              WHERE `set` = "%s" AND `lang` = "%s" AND `code` = "%s"',

        'count' =>
            'SELECT `set`, `lang`, COUNT(1) AS `count`
               FROM `{TABLE}`
              GROUP BY `set`, `lang`',

        'active_language_sets' =>
            'SELECT `code`
               FROM `{TABLE}`
              WHERE `set` = "code_lang" AND `active` = 1',

        'insert' =>
            'INSERT INTO `{TABLE}`
                    (`set`, `lang`, `code`, `desc`, `quantity`, `order`, `active`)
             VALUES ("%s", "%s", "%s", "%s", "%d", "%s", "%d")',

        'update' =>
            'UPDATE `{TABLE}`
                SET `desc` = "%s", `quantity` = "%d", `order` = "%s", `active` = "%d"
              WHERE `set` = "%s" AND `lang` = "%s" AND `code` = "%s"',

        'toggle' =>
            // Toggle integer 0|1 from https://stackoverflow.com/a/1461135
            'UPDATE `{TABLE}`
                SET `active` = 1 - `active`
              WHERE `set` = "%s" AND `lang` = "%s" AND `code` = "%s"',

        'delete' =>
            'DELETE FROM `{TABLE}`
              WHERE `set` = "%s" AND `lang` = "%s" AND `code` = "%s"',

        'remove' => [
            'DELETE FROM `{TABLE}` WHERE `set` = "%s" AND `code` = "%s"',
            'DELETE FROM `{TABLE}` WHERE `set` = "code_admin" AND `code` = "%s"',
            'DELETE FROM `{TABLE}` WHERE `set` = "%s"'
        ]
    ];
}
