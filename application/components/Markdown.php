<?php

    namespace application\components;

    use \Yii;
    use \CException;
    use \Ciconia\Extension\Gfm as Github;
    use \Ciconia\Renderer\RendererInterface;

    class Markdown extends \Ciconia\Ciconia
    {

        /**
         * Initialisation
         *
         * This method is implemented to enable the class as an application component, and
         * to enable the extra extensions we want to use.
         *
         * @access public
         * @return void
         */
        public function init()
        {
            // Add GitHub Markdown Extensions.
            $this->addExtension(new \application\components\markdown\FencedCodeBlockExtension);
            $this->addExtension(new Github\TaskListExtension);
            $this->addExtension(new Github\InlineStyleExtension);
            $this->addExtension(new Github\WhiteSpaceExtension);
            $this->addExtension(new Github\TableExtension);
            $this->addExtension(new Github\UrlAutoLinkExtension);
        }

    }
