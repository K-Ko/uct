<?php
/**
 *
 */
namespace Core;

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

        $isSelect = preg_match('~^select~i', $query);

        if ($isSelect) {
            $explain = null;
            if ($result = parent::query('EXPLAIN '.$query)) {
                $explain = $result->fetch_assoc();
            }
            // Start stopwatch
            $ts = -microtime(true);
        }

        $result = parent::query($query);

        if ($this->errno) {
            throw new \Exception($this->error . ' (' . $query . ')', $this->errno);
        }

        if ($isSelect) {
            // Get query time
            $ts += microtime(true);

            if ($explain['key'] != '') {
                $explain = $explain['key'];
            } elseif ($explain['Extra'] == 'Impossible WHERE noticed after reading const tables') {
                $explain = 'const (full unique key)';
            }

            $this->queries[] = [
                'query' => $query,
                'index' => $explain,
                'time'  => sprintf('%.3f ms', $ts*1000)
            ];
        } else {
            $this->queries[] = $query;
        }

        return $result;
    }
}
