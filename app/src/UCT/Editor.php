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
    /**
     *
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        // Extend queries before parent::__construct()
        $this->queries = array_merge($this->queries, $this->queriesEditor);
    }

    // public function __call($name, $args)
    // {
    //     $trace = debug_backtrace(0, 2);

    //     die(
    //         '<pre>Invalid call: ' . $trace[1]['class'] . $trace[1]['type'] . $trace[0]['args'][0] . '() ' .
    //         'in ' . $trace[1]['class'] . $trace[1]['type'] . $trace[1]['function'] . '() ' .
    //         'in ' . $trace[0]['file'] . '(' . $trace[0]['line'] . ')'
    //     );
    // }

    /**
     * Put a code. Insert, update or delete as appropriate.
     */
    public function putData(
        $set,
        $lang,
        $code,
        $desc,
        $quantity = 0,
        $var = 0,
        $order = 0,
        $active = 1,
        $hint = ''
    ) {
        // Get the existing code info, if any.
        $old = $this->get($set, $lang, $code);

        // Field work.
        if ($lang == $this->native) {
            // Contains quantity-dependent text, also means placeholder
            if ($quantity) {
                $var = 1;
            }
        } else {
            $quantity = $var = $order = $active = 0;
        }

        // Make Unix line endings
        $desc = trim(str_replace(["\r\n", "\r"], "\n", $desc));

        // add, update, or delete
        if ($old) {
            if ($desc <> '') {
                $this->query(
                    $this->queries['update'],
                    $desc,
                    $quantity,
                    $var,
                    $order,
                    $active,
                    $hint,
                    $set,
                    $lang,
                    $code
                );
            } else {
                if ($lang == $this->native) {
                    $this->remove($set, $code);
                } else {
                    $this->query(
                        $this->queries['delete'],
                        $set,
                        $lang,
                        $code
                    );
                }
            }
        } elseif ($desc <> '') {
            $this->query(
                $this->queries['insert'],
                $set,
                $lang,
                $code,
                $desc,
                $quantity,
                $var,
                $order,
                $active,
                $hint
            );
        }
    }

    /**
     * Toggle a code, set active 0|1.
     */
    public function toggleActive($set, $code)
    {
        $this->query($this->queries['toggle'], $set, $code);

        $data = $this->get($set, $this->native, $code);

        return $data ? $data['active'] : 0;
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
                $this->putData(
                    $set,
                    $this->native,
                    $code,
                    $desc,
                    $old['quantity'],
                    $old['var'],
                    $old['order'],
                    $old['active']
                );
            }
        } else {
            // Insert
            $this->putData($set, $this->native, $code, $desc);
        }
    }

    /**
     *
     */
    public function adminGet($set)
    {
        return array_merge(
            [ 'param' => 0, 'slave' => 0, 'multi' => 0 ],
            (array) json_decode($this->param('code_admin', $set), true)
        );
    }

    /**
     *
     */
    public function adminPut($set, $admin = [])
    {
        $admin = array_filter(array_merge(
            [ 'param' => 0, 'slave' => 0, 'multi' => 0 ],
            $admin
        ));

        $this->putData(
            'code_admin',
            $this->native,
            $set,
            count($admin) ? json_encode($admin) : ''
        );
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
        $sets = [];
        foreach ($this->query($this->queries['active-language-sets']) as $set) {
            $sets[] = $set['code'];
        }
        return $sets;
    }

    /**
     * Get the hint for a code
     */
    public function getHint($set, $code, $lang = null)
    {
        if (!$lang) {
            $lang = $this->native;
        }
        $result = $this->query($this->queries['get-hint'], $set, $lang, $code);
        return isset($result[0]['hint']) ? $result[0]['hint'] : '';
    }

    /**
     * Set the hint for a code
     */
    public function setHint($set, $code, $hint)
    {
        $this->query($this->queries['set-hint'], $hint, $set, $this->native, $code);
        return $this;
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

    /**
     * Clone a code
     * /
    public function clone($set, $code, $format = '%s_COPY')
    {
        $new_code = sprintf($format, $code);

        foreach ($this->query($this->queries['clone'], $set, $code) as $row) {
            $this->putData(
                $row['set'],
                $row['lang'],
                $new_code,
                $row['desc'],
                $row['quantity'],
                $row['var'],
                $row['order'],
                $row['active']
            );

            if ($row['hint'] != '') {
                $this->setHint($row['set'], $new_code, $row['hint']);
            }
        }

        return $new_code;
    }

    /**
     * Rename a code
     */
    public function rename($set, $code_old, $code_new)
    {
        return $this->query($this->queries['rename'], $code_new, $set, $code_old);
    }

    /**
     * Remove a code completely.
     */
    public function remove($set, $code)
    {
        $rc = +$this->query($this->queries['remove'][0], $set, $code);

        // Delete whole set?
        if ($set == 'code_set') {
            // Remove code_admin entry
            $rc += $this->query($this->queries['remove'][1], $code);
            // Remove remaining codes
            $rc += $this->query($this->queries['remove'][2], $code);
        }

        return $rc;
    }

    /**
     * Check if a given set is a system set
     *
     * @param string $set
     * @return boolean
     */
    public function isSystemSet($set)
    {
        if (!$this->system_sets) {
            // Lazy load
            $this->system_sets = json_decode($this->param('code_admin', 'code_system'));
        }

        return in_array($set, $this->system_sets);
    }

    public function checkLogin($user, $password)
    {
        $data = $this->query($this->queries['get-app-for-user'], $user, $password);
        return !empty($data) ? $data[0] : null;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    protected $queriesEditor = [
        'get-app-for-user' =>
            'SELECT u.`app` AS `id`
                  , a.`desc` AS `name`
               FROM `{{TABLE}}` u
               LEFT JOIN `{{TABLE}}` a
                 ON a.`app` = 0
                AND a.`set` = "code_app"
                AND a.`lang` = "{{NATIVE}}"
                AND a.`code` = u.`app`
              WHERE u.`set` = "code_user"
                AND u.`lang` = "{{NATIVE}}"
                AND u.`code` = "%s"
                AND u.`desc` = SHA1("%s")
            LIMIT 1',

        'get-hint' =>
            'SELECT `hint`
               FROM `{{TABLE}}`
              WHERE `app` = {{APP}}
                AND `set` = "%s"
                AND `lang` = "%s"
                AND `code` = "%s"
              LIMIT 1',

        'set-hint' =>
            'UPDATE `{{TABLE}}`
                SET `hint` = "%s"
              WHERE `app` = {{APP}}
                AND `set` = "%s"
                AND `lang` = "%s"
                AND `code` = "%s"',

        'count' =>
            'SELECT `set`, `lang`, COUNT(1) AS `count`
               FROM `{{TABLE}}`
              WHERE `app` = {{APP}}
              GROUP BY `set`, `lang`',

        'active-language-sets' =>
            'SELECT `code`
               FROM `{{TABLE}}`
              WHERE `app` = {{APP}}
                AND `set` = "code_lang"
                AND `lang` = "{{NATIVE}}"
                AND `active` = 1',

        'insert' =>
            'INSERT INTO `{{TABLE}}`
                    (`app`,   `set`, `lang`, `code`, `desc`, `quantity`, `var`, `order`, `active`, `hint`)
             VALUES ({{APP}}, "%s",  "%s",   "%s",   "%s",   %d,         %d,    "%s",    %d,       "%s")',

        'update' =>
            'UPDATE `{{TABLE}}`
                SET `desc` = "%s", `quantity` = %d, `var` = %d, `order` = %d, `active` = %d, `hint` = "%s"
              WHERE `set` = "%s" AND `lang` = "%s" AND `code` = "%s"',

        'rename' =>
            'UPDATE `{{TABLE}}`
                SET `code` = "%s"
              WHERE `app` = {{APP}}
                AND `set` = "%s"
                AND `code` = "%s"',

        'toggle' =>
            // Toggle integer 0 <> 1 from https://stackoverflow.com/a/1461135
            'UPDATE `{{TABLE}}`
                SET `active` = 1 - `active`
              WHERE `app` = {{APP}}
                AND `set` = "%s"
                AND `lang` = "{{NATIVE}}"
                AND `code` = "%s"',

        // 'clone' =>
        //     // All languages
        //     'SELECT * FROM `{{TABLE}}`
        //       WHERE `app` = {{APP}}
        //         AND `set` = "%s"
        //         AND `code` = "%s"',

        'delete' =>
            'DELETE FROM `{{TABLE}}`
              WHERE `app` = {{APP}}
                AND `set` = "%s"
                AND `lang` = "%s"
                AND `code` = "%s"',

        'remove' => [
            'DELETE FROM `{{TABLE}}`
              WHERE `app` = {{APP}} AND `set` = "%s" AND `code` = "%s"',
            'DELETE FROM `{{TABLE}}`
              WHERE `app` = {{APP}} AND `set` = "code_admin" AND `code` = "%s"',
            'DELETE FROM `{{TABLE}}`
              WHERE `app` = {{APP}} AND `set` = "%s"'
        ],
    ];


    protected $system_sets;

    /**
     *
     */
    protected function queryRaw($query)
    {
        $args = func_get_args();
        return $this->db->query(vsprintf($this->sql(array_shift($args)), $args));
    }

    protected function exception($msg)
    {
        $args = func_get_args();
        $msg = array_shift($args);
        throw new Exception(vsprintf($msg, $args));
    }
}
