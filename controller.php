<?php

/**
 * @project:   Event Based Mail Scheduler
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\EventBasedMailScheduler;

use Bitter\EventBasedMailScheduler\Console\Command\ScanChanges;
use Concrete\Core\Package\Package;

class Controller extends Package
{
    protected $pkgHandle = 'event_based_mail_scheduler';
    protected $pkgVersion = '0.0.3';
    protected $appVersionRequired = '9.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/EventBasedMailScheduler' => 'Bitter\EventBasedMailScheduler',
    ];
    protected $packageDependencies = [
        "simple_newsletter" => "1.2.7"
    ];

    public function getPackageDescription()
    {
        return t('Send out email campaigns with Simple Newsletter add-on based on given Concrete CMS events.');
    }

    public function getPackageName()
    {
        return t('Event-based Mail Scheduler');
    }

    public function on_start()
    {
        if ($this->app->isRunThroughCommandLineInterface()) {
            $console = $this->app->make('console');
            $console->add(new ScanChanges());
        }
    }

    public function install()
    {
        parent::install();
        $this->installContentFile('install.xml');
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile('install.xml');
    }

}