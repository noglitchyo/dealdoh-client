<?php declare(strict_types=1);

namespace NoGlitchYo\DealdohClient\Action\Command;

use NoGlitchYo\Dealdoh\Entity\DnsUpstreamPool;
use NoGlitchYo\DealdohClient\Domain\Exception\UpstreamPoolConfigException;
use NoGlitchYo\DealdohClient\Domain\UpstreamPoolLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class ListUpstreamCommand extends Command
{
    public const NAME = 'upstream:list';

    /**
     * @var string
     */
    private $upstreamPoolFilePath;

    public function __construct(string $upstreamPoolFilePath)
    {
        $this->upstreamPoolFilePath = $upstreamPoolFilePath;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(static::NAME)
            ->setDescription(
                sprintf('List DNS upstream from %s.', pathinfo($this->upstreamPoolFilePath, PATHINFO_FILENAME))
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $dnsUpstreamPool = DnsUpstreamPool::fromJson(UpstreamPoolLoader::load($this->upstreamPoolFilePath));
            $io->listing($dnsUpstreamPool->getUpstreams());
        } catch (UpstreamPoolConfigException $e) {
            $output->writeln(
                sprintf(
                    '<comment>%s : Run `upstream:add` command to automatically create %s.</comment>',
                    $e->getMessage(),
                    $this->upstreamPoolFilePath
                )
            );
        } catch (Throwable $t) {
            $output->writeln('<comment>' . $t->getMessage() . '</comment>');
        }
    }
}
