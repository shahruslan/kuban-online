<?php

namespace KubanOnline\Commands;

use KubanOnline\Exceptions\JsonDecodeException;
use KubanOnline\Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDoctorCommand extends Command
{
    private Service $service;

    protected static string $defaultName = 'doctor:check';

    protected function configure(): void
    {
        $this
            ->setDescription('Доступность записи к врачу')
            ->setHelp('Проверяет доступность записи к врачу и уведомляет о состоянии')
            ->addArgument('doctor', InputArgument::REQUIRED, 'Идентификатор доктора')
            ->addOption('force', 'f',  InputOption::VALUE_NONE, 'Принудительный показ статуса')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->service = new Service();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $doctor = $input->getArgument('doctor');
            $isForce = $input->getOption('force');
            $this->service->check($doctor, $isForce);
        } catch (JsonDecodeException $exception) {
            echo 'exception: ' . $exception->getResponse();
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}