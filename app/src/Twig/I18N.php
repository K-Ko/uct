<?php
/**
 *
 */
namespace Twig;

/**
 *
 */
use RuntimeException;
use UCT\UCT;

/**
 *
 */
class I18N
{
    /**
     *
     */
    public function __construct(UCT $uct, $options = [])
    {
        $this->uct = $uct;
        $this->options = $options;

        if (!isset($this->options['language'])) {
            $this->options['language'] = $uct->native;
        }
    }

    /**
     *
     */
    public function setSet($set)
    {
        $this->options['set'] = $set;
        return $this;
    }

    /**
     *
     */
    public function setLanguage($language)
    {
        $this->options['language'] = $language;
        return $this;
    }

    /**
     * Shortcut for templates
     */
    public function __get($code)
    {
        list($set, $code) = $this->set($code);
        return $this->render($set, $code);
    }

    /**
     * Shortcut for templates
     */
    public function __call($code, $args)
    {
        list($set, $code) = $this->set($code);
        return $this->render($set, $code, $args);
    }

    /**
     * Shortcut for templates when a subcode like "row.cell.code" is needed
     */
    public function h($code)
    {
        $args = func_get_args();

        if ($code) {
            $code = array_shift($args);
            list($set, $code) = $this->set($code);
            return $this->render($set, $code, $args);
        }
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $uct;

    /**
     *
     */
    protected $options;

    /**
     * Extract code set from code or use default code set
     *
     * @throw \RuntimeException If no code set was found
     * @param string $code
     * @return array Contains [ set, code ]
     */
    protected function set($code)
    {
        $set_code = preg_split('~(__|::)~', $code, 2);

        if (count($set_code) == 2) {
            // Named code set given
            return $set_code;
        }

        if (!empty($this->options['set'])) {
            // Default code set defined
            return [ $this->options['set'], $code ];
        }

        // No usable code set found
        throw new RuntimeException(__CLASS__ . ': Missing code set!');
    }

    /**
     *
     */
    protected function render($set, $code, $args = [])
    {
        $desc = $this->uct->render($set, $this->options['language'], $code, $args);
        // Mark missing translations as set::code
        return $desc ?: $set . '::' . $code;
    }
}
