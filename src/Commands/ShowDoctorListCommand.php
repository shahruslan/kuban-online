<?php


namespace KubanOnline\Commands;


use KubanOnline\Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowDoctorListCommand extends Command
{
    private Service $service;

    protected static string $defaultName = 'doctor:list';

    protected function configure(): void
    {
        $this
            ->setDescription('Показать список врачей')
            ->setHelp('Показывает список врачей и их идентификаторы')
            ->addArgument('speciality', InputArgument::REQUIRED, 'Идентификатор специальности')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->service = new Service();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $speciality = $input->getArgument('speciality');
        $doctors = $this->service->doctors($speciality);

        foreach ($doctors as $doctor) {
            $message = " <info>$doctor->id</info> \t $doctor->name (<comment>$doctor->tickets</comment>)";
            $output->writeln($message);
        }

        return Command::SUCCESS;
    }
}