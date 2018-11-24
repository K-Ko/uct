<?php
/**
 *
 */
namespace Core;

/**
 *
 */
use UCT\UCT;
use Kaoken\MarkdownIt\MarkdownIt;

/**
 *
 */
class I18N
{
    /**
     *
     */
    public function __construct(UCT $uct, $set)
    {
        $this->uct = $uct;
        $this->set = $set;
        $this->language = $this->uct->native;
        $this->md = new MarkdownIt([
            'html'        => true,
            'linkify'     => true,
            'typographer' => true
        ]);
    }

    /**
     *
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Return always true, missing translations will be handled by render()
     */
    public function __isset($code)
    {
        return true;
    }

    /**
     * Shortcut for templates
     */
    public function __get($code)
    {
        return $this->render($code);
    }

    /**
     * Shortcut for templates
     */
    public function __call($code, $args)
    {
        return $this->render($code, $args);
    }

    /**
     * Shortcut for templates when a subcode like "row.cell.code" is needed
     */
    public function h($code)
    {
        $args = func_get_args();
        $code = array_shift($args);
        return $this->render($code, $args);
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
    protected $set;

    /**
     *
     */
    protected $language;

    /**
     *
     */
    protected $md;

    /**
     *
     */
    protected function render($code, $args = [])
    {
        $code = explode('::', $code);

        if (count($code) == 1) {
            $set  = $this->set;
            $code = $code[0];
        } else {
            $set  = $code[0];
            $code = $code[1];
        }

        $desc = $this->uct->render($set, $this->language, $code, $args);

        if ($desc == '') {
            $desc = $code;
        }

        if ($desc == $code) {
            // "UpperCamelCase" > "Upper Camel Case"
            return trim(preg_replace('~[A-Z]~', ' $0', $desc));
        }

        return $this->md->renderInline($desc);
    }
}
