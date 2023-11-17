<?php

namespace App\Command;

use App\Manager\RoleManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// Command for make admin user
#[AsCommand(name: 'app:makeAdmin', description: 'Promotes user to be admin')]
class MakeAdminCommand extends Command
{
    public function __construct(private RoleManager $roleManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('user-id', InputArgument::REQUIRED, 'User ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = (int) $input->getArgument('user-id');
        $this->roleManager->grantAdmin($userId);

        return Command::SUCCESS;
    }
}
