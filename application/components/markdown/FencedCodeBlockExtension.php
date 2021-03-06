<?php

    namespace application\components\markdown;

    use \Ciconia\Common\Text;
    use \Ciconia\Markdown;

    /**
     * Markdown converts text with four spaces at the front of each line to code blocks.
     * GFM supports that, but we also support fenced blocks.
     * Just wrap your code blocks in ``` and you won't need to indent manually to trigger a code block.
     *
     * PHP Markdown style `~` is also available.
     *
     * @author Kazuyuki Hayashi <hayashi@valnur.net>
     */
    class FencedCodeBlockExtension extends \Ciconia\Extension\Gfm\FencedCodeBlockExtension
    {

        /**
         * @access private
         * @var Markdown $markdown
         */
        private $markdown;

        /**
         * Register
         *
         * @access public
         * @param Markdown $markdown "Markdown instance"
         * @return void
         */
        public function register(Markdown $markdown)
        {
            $this->markdown = $markdown;
            // Should be run before first hashHtmlBlocks().
            $markdown->on('initialize', array($this, 'processFencedCodeBlock'));
        }

        /**
         * Process: Fenced Code Block
         *
         * @access public
         * @param Text $text "The text to be processed."
         * @return string
         */
        public function processFencedCodeBlock(Text $text)
        {
            $regex = '{
                (?:\n+|\A)
                (?:
                    ([`~]{3})[ ]*         #1 fence ` or ~
                        ([a-zA-Z0-9]*?)?  #2 language [optional]
                    \n+
                    (.*?)\n                #3 code block
                    \1                    # matched #1
                )
            }smx';
            $text->replace($regex, function(Text $w, Text $fenced, Text $lang, Text $code) {
                $options = array();
                if(!$lang->isEmpty()) {
                    $options = array(
                        'attr' => array(
                            'class' => 'prettyprint lang-' . $lang->lower(),
                        ),
                    );
                }
                $code->escapeHtml(ENT_NOQUOTES);
                $this->markdown->emit('detab', array($code));
                $code->replace('/\A\n+/', '');
                $code->replace('/\s+\z/', '');
                return "\n\n" . $this->getRenderer()->renderCodeBlock($code, $options) . "\n\n";
            });
        }

    }
