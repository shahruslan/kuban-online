<?php


namespace KubanOnline\Dto;


use Spatie\DataTransferObject\DataTransferObject;

class Doctor extends DataTransferObject
{
    public string $id;
    public string $name;
    public int $tickets;
}