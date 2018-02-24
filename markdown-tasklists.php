<?php

namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

class MarkdownTaskListsPlugin extends Plugin
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onMarkdownInitialized' => ['onMarkdownInitialized', 0],
            'onTwigSiteVariables'   => ['onTwigSiteVariables', 0]
        ];
    }

    public function onMarkdownInitialized(Event $event)
    {
        $markdown = $event['markdown'];

        $markdown->addBlockType('-', 'List', false, true);

        $markdown->blockListComplete = function(array $Block) {
            if ($Block['pattern'] === '[*+-]') {
                $containsTaskList = false;
                foreach ($Block['element']['text'] as &$Item) {
                    foreach ($Item['text'] as &$Text) {
                        $prefix = substr(trim($Text), 0, 4);
                        if ($prefix === '[ ] ' || $prefix === '[x] ') {
                            $containsTaskList = true;
                            $Item['attributes'] = ['class' => 'task-list-item'];
                            $Text = sprintf(
                                '<input class="task-list-item-checkbox" type="checkbox" %s> %s',
                                $prefix === '[x] ' ? 'checked disabled' : 'disabled',
                                substr(trim($Text), 4)
                            );
                        }
                    }
                }
                if ($containsTaskList) {
                    $Block['element']['attributes'] = ['class' => 'contains-task-list'];
                }
            }
            return $Block;
        };
    }

    public function onTwigSiteVariables()
    {
        $this->grav['assets']->add('plugin://markdown-tasklists/assets/tasklists.css');
    }
}
