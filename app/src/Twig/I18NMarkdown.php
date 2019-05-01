<?php
/**
 *
 */
namespace Twig;

/**
 *
 */
use UCT\UCT;
use Kaoken\MarkdownIt\MarkdownIt;

/**
 *
 */
class I18NMarkdown extends I18N
{
    /**
     *
     */
    public function __construct(UCT $uct, $options = [])
    {
        parent::__construct($uct, $options);

        $this->md = new MarkdownIt([
            'html'        => true,
            'linkify'     => true,
            'typographer' => true
        ]);
    }

    /**
     * Just render a text
     */
    public function md($text)
    {
        return nl2br($this->md->renderInline($text));
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $md;

    /**
     *
     */
    protected function render($set, $code, $args = [])
    {
        return nl2br($this->md->renderInline(parent::render($set, $code, $args)));
    }
}
