<?php declare(strict_types=1);

namespace NoGlitchYo\DealdohClient\Action\Command;

use InvalidArgumentException;
use NoGlitchYo\DealdohClient\Domain\Service\UpstreamPool\AddUpstreamService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class AddUpstreamCommand extends Command
{
    public const NAME = 'upstream:add';

    /**
     * @var string
     */
    private $upstreamPoolFilePath;
    /**
     * @var AddUpstreamService
     */
    private $addUpstreamService;

    public function __construct(string $upstreamPoolFilePath, AddUpstreamService $addUpstreamService)
    {
        $this->upstreamPoolFilePath = $upstreamPoolFilePath;
        $this->addUpstreamService = $addUpstreamService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(static::NAME)
            ->setDescription(sprintf('Add a DNS upstream to %s file.', $this->upstreamPoolFilePath))
            ->setHelp('Provide a valid upstream (i.e: dns://, .')
            ->addArgument(
                'uri',
                InputArgument::REQUIRED,
                'URI of your upstream (i.e: "https://dns.google.com/resolve", "8.8.8.8:53", 
                "https://dns.google.com/resolve")'
            )->addArgument(
                'code',
                InputArgument::OPTIONAL,
                'An identifier for your upstream (i.e: "local", "google", "cloudflare")'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $upstreamCode = $input->getArgument('code');
        $upstreamUri = $input->getArgument('uri');

        if (!empty($upstreamCode) && !is_string($upstreamCode)) {
            throw new InvalidArgumentException('Upstream code must be a string');
        }

        if (!is_string($upstreamUri)) {
            throw new InvalidArgumentException('Upstream URI must be a string.');
        }

        try {
            $this->addUpstreamService->add($upstreamUri, $upstreamCode);
            $io->success(sprintf("Added upstream `%s` successfully.", $upstreamUri));
        } catch (Throwable $t) {
            $io->error(sprintf("Failed to configure DNS %s file.", pathinfo($this->upstreamPoolFilePath, PATHINFO_BASENAME)));
            $output->writeln('<comment>' . $t->getMessage() . '</comment>');
        }
    }
}
