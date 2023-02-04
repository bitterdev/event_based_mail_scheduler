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

class Config implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $config;
    protected $campaign;

    public function __construct(
        Repository $config,
        Campaign   $campaign
    )
    {
        $this->config = $config;
        $this->campaign = $campaign;
    }

    public function getCampaigns(): array
    {
        return $this->campaign->getList();
    }

    public function getSelectedCampaign(): string
    {
        return $this->config->get("event_based_mail_scheduler.selected_campaign", "");
    }

    public function setSelectedCampaign(string $selectedCampaign)
    {
        $this->config->save("event_based_mail_scheduler.selected_campaign", $selectedCampaign);
    }
}