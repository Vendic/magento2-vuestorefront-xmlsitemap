<?php
namespace Vendic\VueStorefrontSitemap\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vendic\VueStorefrontSitemap\Cron\GenerateSitemap;

class RunGenerateSitemap extends Command
{
    /**
     * @var GenerateSitemap
     */
    protected $generateSitemapCron;

    public function __construct(
        GenerateSitemap $generateSitemapCron
    ) {
        $this->generateSitemapCron = $generateSitemapCron;
        parent::__construct();
    }
    protected function configure()
    {
        $this->setName('vsf:sitemap:generate');
        $this->setDescription('Generate VSF Sitemap');
        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->generateSitemapCron->execute();
            $output->writeln("VSF Sitemap Generated");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
