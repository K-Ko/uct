<?php
/**
 *
 */
namespace App;

/**
 *
 */
class MySQLi extends \MySQLi
{
    /**
     * log explain select ...
     */
    public $verbose = false;

    /**
     *
     */
    public $queries = [];

    /**
     * Overwrite for query logging
     */
    public function query($query, $resultmode = MYSQLI_STORE_RESULT)
    {
        if (!$this->verbose) {
            return parent::query($query);
        }

        $isSelect = preg_match('~^SELECT~i', $query);

        $explain = null;

        if ($isSelect) {
            if ($result = parent::query('EXPLAIN '.$query)) {
                $explain = $result->fetch_assoc();
            }
        }

        // Start stopwatch
        $ts = -microtime(true);

        $result = parent::query($query);

        // Get query time
        $ts += microtime(true);

        if ($this->errno) {
            throw new \Exception($this->error . ' (' . $query . ')', $this->errno);
        }

        if ($isSelect) {
            if ($explain['key'] != '') {
                $explain = $explain['key'];
            } elseif ($explain['Extra'] == 'Impossible WHERE noticed after reading const tables') {
                $explain = 'const (full unique key)';
            }
        }

        $this->queries[] = [
            'ts'     => microtime(true),
            'query'  => $query,
            'result' => preg_replace('~\s+~', ' ', print_r($result, true)),
            'index'  => $explain,
            'time'   => sprintf('%.3f ms', $ts*1000)
        ];

        return $result;
    }
}
