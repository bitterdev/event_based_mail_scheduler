<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\EventBasedMailScheduler;

use Bitter\SimpleNewsletter\Service\Campaign;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Type\Type;

class Config implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $config;
    protected $pageType;
    protected $campaign;

    public function __construct(
        Repository $config,
        Type       $pageType,
        Campaign   $campaign
    )
    {
        $this->config = $config;
        $this->pageType = $pageType;
        $this->campaign = $campaign;
    }

    public function getCampaigns(): array
    {
        return $this->campaign->getList();
    }

    public function getPageTypes(): array
    {
        $pageTypes = [];

        foreach ($this->pageType->getList() as $pageType) {
            /** @var Type $pageType */
            $pageTypes[$pageType->getPageTypeID()] = $pageType->getPageTypeDisplayName();
        }

        return $pageTypes;
    }

    public function getEvents(): array
    {
        return [
            "on_page_add" => t("Add Page"),
            "on_page_duplicate" => t("Duplicate Page"),
            "on_page_update" => t("Update Page")
        ];
    }

    public function getTime(): string
    {
        return $this->config->get("event_based_mail_scheduler.time", "20:00");
    }


    public function setTime(string $time)
    {
        $this->config->save("event_based_mail_scheduler.time", $time);
    }

    public function getSelectedCampaign(): string
    {
        return $this->config->get("event_based_mail_scheduler.selected_campaign", "");
    }


    public function setIsEnabled(bool $isEnabled)
    {
        $this->config->save("event_based_mail_scheduler.is_enabled", $isEnabled);
    }

    public function isEnabled(): bool
    {
        return $this->config->get("event_based_mail_scheduler.is_enabled", false);
    }


    public function setSelectedCampaign(string $selectedCampaign)
    {
        $this->config->save("event_based_mail_scheduler.selected_campaign", $selectedCampaign);
    }

    public function getSelectedEvents(): array
    {
        return $this->config->get("event_based_mail_scheduler.selected_events", $this->getEvents());
    }

    public function setSelectedEvents(array $selectedEvents)
    {
        $this->config->save("event_based_mail_scheduler.selected_events", $selectedEvents);
    }

    public function getSelectedPageTypes(): array
    {
        return $this->config->get("event_based_mail_scheduler.selected_page_types", []);
    }


    public function setSelectedPageTypes(array $selectedPageTypes)
    {
        $this->config->save("event_based_mail_scheduler.selected_page_types", $selectedPageTypes);
    }

}