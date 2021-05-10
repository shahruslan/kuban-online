<?php


namespace KubanOnline\Commands;


use KubanOnline\Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowSpecialityListCommand extends Command
{
    private Service $service;

    protected static $defaultName = 'speciality:list';

    protected function configure(): void
    {
        $this
            ->setDescription('Показать список специальностей')
            ->setHelp('Показывает список специальностей и их идентификаторы')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->service = new Service();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $specialities = $this->service->specialities();

        foreach ($specialities as $speciality) {
            $message = " <info>$speciality->id</info> \t $speciality->name (<comment>$speciality->tickets</comment>)";
            $output->writeln($message);
        }

        return Command::SUCCESS;
    }
}