<?php

/**
 * @project:   Event Based Mail Scheduler
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\EventBasedMailScheduler\Console\Command;

use Bitter\EventBasedMailScheduler\Config;
use Bitter\SimpleNewsletter\Entity\Campaign;
use Bitter\SimpleNewsletter\Enumeration\CampaignState;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScanChanges extends Command
{
    protected function configure()
    {
        $this
            ->setName('event-based-mail-scheduler:scan-changes')
            ->setDescription('Scan for changes.');
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();
        /** @var Config $config */
        $config = $app->make(Config::class);
        /** @var PageList $pageList */
        $pageList = $app->make(PageList::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var Date $dateHelper */
        $dateHelper = $app->make(Date::class);

        $io = new SymfonyStyle($input, $output);

        $now = new \DateTime();

        $pageList->ignorePermissions();

        $pages = $pageList->getResults();

        $rows = [];

        foreach ($pages as $page) {

            if ($page instanceof Page) {
                $isScheduled = (bool)$page->getAttribute("newsletter_scheduled");
                $scheduledAt = $page->getAttribute("newsletter_scheduled_at");

                if ($isScheduled && $scheduledAt instanceof \DateTime) {
                    if ($now->getTimestamp() >= $scheduledAt->getTimestamp()) {
                        $masterCampaign = $entityManager->getRepository(Campaign::class)->findOneBy(["id" => $config->getSelectedCampaign()]);

                        if ($masterCampaign instanceof Campaign) {
                            $duplicatedCampaign = new Campaign();
                            $duplicatedCampaign->setBody($masterCampaign->getBody());
                            $duplicatedCampaign->setCreatedAt($now);
                            $duplicatedCampaign->setMailingList($masterCampaign->getMailingList());
                            $duplicatedCampaign->setName($masterCampaign->getName());
                            $duplicatedCampaign->setSubject($masterCampaign->getSubject());
                            $duplicatedCampaign->setState(CampaignState::QUEUED);

                            $entityManager->persist($duplicatedCampaign);
                            $entityManager->flush();

                            $page->setAttribute("newsletter_scheduled", false);
                        }
                    }

                    $rows[] = [
                        $page->getCollectionName(),
                        $page->getCollectionID(),
                        $dateHelper->formatDateTime($scheduledAt),
                        $page->getAttribute("newsletter_scheduled") ? "No" : "Yes"
                    ];
                }
            }
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Page', 'CID', 'Newsletter Scheduled At', 'Processed'])
            ->setRows($rows)
        ;
        $table->render();

        return static::SUCCESS;
    }
}
