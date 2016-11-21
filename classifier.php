<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Utils;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class ClassifierPlugin
 * @package Grav\Plugin
 */
class ClassifierPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main event we are interested in
        $this->enable([
            'onPageInitialized' => ['onPageInitialized', 0]
        ]);
    }

    public function onPageInitialized()
    {
        if ($this->isAdmin()) {
            $this->active = false;
            return;
        }

        $defaults = (array) $this->config->get('plugins.classifier');
        /** @var Page $page */
        $page = $this->grav['page'];
        if (isset($page->header()->classifier)) {
            $this->config->set('plugins.classifier', array_merge($defaults, $page->header()->classifier));
        }
        if ($this->config->get('plugins.classifier.active')) {
            $this->enable([
                'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
                'onPageContentProcessed' => ['onPageContentProcessed', -1000]
            ]);
        }
    }

    public function onTwigSiteVariables()
    {
        $this->grav['assets']
            -> add('theme://assets/classifier.css');
    }

    public function onPageContentProcessed(Event $e)
    {
        $page = $this->grav['page'];
        $output = $page->getRawContent();
        $header = $page->header();

        foreach ($header->classifier['tags'] as $tag) {
            $output = $this->insertClass($output, $tag['tag'], $tag['nums'], $tag['class']);
        }

        $this->grav['page']->setRawContent($output);
    }

    private function insertClass($content, $tagname, $selector, $classname) {
        if ($selector !== null) {
            if ($selector === '\*') {
                $selector = '*';
            }
            // strip space characters
            $nums = str_replace(' ', '', $selector);
            // explode on the comma
            $nums = explode(',', $nums);

            // Get count of requested tags in the output
            $tagcount = substr_count($content, '<'.$tagname);
            $offset = 0;
            for ($i=1; $i<=$tagcount; $i++) {
                // Get pos of first tag
                $pos = strpos($content, '<'.$tagname, $offset);
                $str1 = substr($content, 0, $pos);
                $str2 = substr($content, $pos);
                // Are we supposed to touch this tag?
                if ( ($selector === '*') || (in_array($i, $nums)) ) {
                    // Get full first tag
                    preg_match('/\<'.$tagname.'.*?\>/', $str2, $matches);
                    $fulltag = $matches[0];

                    // add class
                    if (strpos($fulltag, 'class=') !== false) {
                        $fulltag = str_replace('class="', 'class="'.$classname.' ', $fulltag);
                    } else {
                        if (Utils::endsWith($fulltag, '/>')) {
                            $fulltag = str_replace('/>', ' class="'.$classname.'" />', $fulltag);
                        } else {
                            $fulltag = str_replace('>', ' class="'.$classname.'">', $fulltag);
                        }
                    }

                    // replace existing <table> tag with modified one
                    $str2 = preg_replace('/'.preg_quote($matches[0], '/').'/', $fulltag, $str2, 1);
                    $content = $str1.$str2;
                }
                // move offset
                $offset = strpos($content, '<'.$tagname, $offset) + 1;
            }
        }
        return $content;
    }
}
