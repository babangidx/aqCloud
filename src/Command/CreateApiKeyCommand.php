<?php

namespace App\Command;

use App\Entity\ApiKey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-api-key')]
class CreateApiKeyCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('user1', InputArgument::REQUIRED, 'The user associated with this API key')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user1 = $input->getArgument('user1');
        $token = bin2hex(random_bytes(16));

        $apiKey = new ApiKey();
        $apiKey->setUser1($user1);
        $apiKey->setToken($token);
        $apiKey->setCreatedAt(new \DateTime());

        $this->entityManager->persist($apiKey);
        $this->entityManager->flush();

        $output->writeln('API Key created successfully:');
        $output->writeln("User: $user1");
        $output->writeln("Token: $token");

        return Command::SUCCESS;
    }
}
