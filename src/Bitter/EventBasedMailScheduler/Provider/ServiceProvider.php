<?php

/**
 * @project:   Event Based Mail Scheduler
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\EventBasedMailScheduler\Provider;

use Bitter\EventBasedMailScheduler\Config;
use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Page\DuplicatePageEvent;
use Concrete\Core\Page\Event;
use Concrete\Core\Page\Page;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServiceProvider extends Provider
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var Config */
    protected $config;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->eventDispatcher = $this->app->make(EventDispatcherInterface::class);
        $this->config = $this->app->make(Config::class);
    }

    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function register()
    {
        foreach ($this->config->getSelectedEvents() as $eventName) {
            $this->eventDispatcher->addListener($eventName, function ($event) {
                $page = null;

                if ($event instanceof Event) {
                    $page = $event->getPageObject();
                } else if ($event instanceof DuplicatePageEvent) {
                    $page = $event->getPageObject();
                }

                if ($page instanceof Page) {
                    if (in_array($page->getPageTypeID(), $this->config->getSelectedPageTypes())) {
                        $this->config->setIsEnabled(true);
                    }
                }
            });
        }
    }
}