<?php

namespace Media\Backend;


use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

class MediaBackend implements EventListenerInterface
{

    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @see EventListenerInterface::implementedEvents()
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            'Backend.Menu.get' => 'getBackendMenu',
            'Backend.View.initialize' => 'initializeBackendView'
        ];
    }

    public function initializeBackendView(Event $event)
    {
        $event->subject()->loadHelper('Media.Media');
        $event->subject()->loadHelper('Media.MediaPicker');
    }

    public function getBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Media',
            'url' => ['plugin' => 'Media', 'controller' => 'MediaBrowser', 'action' => 'index'],
            'data-icon' => 'file image outline'
        ]);
    }
}