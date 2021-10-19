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

    protected $state;

    public function __construct(
        \Magento\Framework\App\State $state,
        GenerateSitemap $generateSitemapCron
    ) {
        $this->generateSitemapCron = $generateSitemapCron;
        $this->state = $state;
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
            $this->state->emulateAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND, [$this->generateSitemapCron, 'execute']);
            $output->writeln("VSF Sitemap Generated");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
