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
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();
        /** @var Config $config */
        $config = $app->make(Config::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);

        $io = new SymfonyStyle($input, $output);

        if ($config->isEnabled()) {
            $io->info("Enabled.");

            $now = new \DateTime();

            $scheduledAt = clone $now;
            list($hour, $minute) = explode(":", $config->getTime());
            $scheduledAt->setTime((int)$hour, (int)$minute);

            if ($now->getTimestamp() >= $scheduledAt->getTimestamp()) {
                $io->info("Time reached.");

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

                    $config->setIsEnabled(false);

                    $io->success("Duplicated campaign and add to queue.");
                }
            } else {
                $io->info("Time not reached.");

            }
        } else {
            $io->info("Not enabled.");
        }

        return static::SUCCESS;
    }
}
