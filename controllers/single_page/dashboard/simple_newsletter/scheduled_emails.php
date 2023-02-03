<?php

/**
 * @project:   Event Based Mail Scheduler
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\EventBasedMailScheduler\Controller\SinglePage\Dashboard\SimpleNewsletter;

use Bitter\EventBasedMailScheduler\Config;
use Bitter\SimpleNewsletter\Service\Campaign;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Type\Type;

class ScheduledEmails extends DashboardPageController
{
    /** @var Request */
    protected $request;
    /** @var Config */
    protected $config;
    /** @var Validation */
    protected $formValidator;

    public function on_start()
    {
        parent::on_start();

        $this->request = $this->app->make(Request::class);
        $this->config = $this->app->make(Config::class);
        $this->formValidator = $this->app->make(Validation::class);
    }

    public function view()
    {
        if ($this->request->getMethod() === 'POST') {
            $this->formValidator->setData($this->request->request->all());

            $this->formValidator->addRequiredToken("update_settings");

            if (!$this->formValidator->test()) {
                $this->error = $this->formValidator->getError();
            }

            if (!$this->error->has()) {
                $this->config->setSelectedPageTypes((array)$this->request->request->get("selectedPageTypes"));
                $this->config->setSelectedEvents((array)$this->request->request->get("selectedEvents"));
                $this->config->setSelectedCampaign((string)$this->request->request->get("selectedCampaign"));
                $this->config->setTime($this->request->request->get("time"));

                $this->set('success', t("The settings has been updated successfully."));
            }
        }

        $this->set('selectedPageTypes', $this->config->getSelectedPageTypes());
        $this->set('selectedEvents', $this->config->getSelectedEvents());
        $this->set('selectedCampaign', $this->config->getSelectedCampaign());
        $this->set('time', $this->config->getTime());
        $this->set('pageTypes', $this->config->getPageTypes());
        $this->set('events', $this->config->getEvents());
        $this->set('campaigns', $this->config->getCampaigns());
    }

}
