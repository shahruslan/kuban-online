<?php

namespace KubanOnline\Commands;

use KubanOnline\Exceptions\JsonDecodeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDoctorCommand extends Command
{
    protected static $defaultName = 'doctor:check';

    protected function configure(): void
    {
        $this
            ->setDescription('Доступность записи к врачу.')
            ->setHelp('Проверяет доступность записи к врачу и уведомляет о состоянии')
            ->addArgument('doctor', InputArgument::REQUIRED, 'Идентификатор доктора')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        try {
            (new \KubanOnline\Service)->check($input->getArgument('doctor'));
        } catch (JsonDecodeException $exception) {
            echo 'ex: ' . $exception->getResponse();
        }


        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;
    }
}