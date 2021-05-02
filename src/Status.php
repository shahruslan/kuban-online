<?php


namespace KubanOnline;


class Status
{
    const OPEN = 1;
    const CLOSE = 2;
    const ERROR = 3;

    private int $status;

    private function __construct(int $status)
    {
        $this->status = $status;
    }

    public function text(): string
    {
        $fields = [
            self::OPEN => 'Запись открыта',
            self::CLOSE => 'Запись закрыта',
            self::ERROR => 'Ошибка',
        ];

        return $fields[$this->status];
    }

    public function status(): int
    {
        return $this->status;
    }

    public function statusText(?Status $prevStatus = null): string
    {
        $data = [
            '1-2' => 'Запись закрылась',
            '1-3' => 'Ошибка после открытия',
            '2-1' => 'Запись открылась',
            '2-3' => 'Ошибка после закрытия',
            '3-1' => 'Запись открылась после ошибки',
            '3-2' => 'Запись закрылась после ошибки',

            '1' => 'Запись открыта',
            '2' => 'Запись закрыта',
            '3' => 'Ошибка',
        ];

        $key = ($prevStatus ? $prevStatus->status() . '-' : '') . $this->status();
//        $key = $prevStatus->status() . '-' . $this->status();

        return $data[$key];
    }

    public function isOpen(): bool
    {
        return $this->status == $this::OPEN;
    }

    public function isClosed(): bool
    {
        return $this->status == $this::CLOSE;
    }

    public function isError(): bool
    {
        return $this->status == $this::ERROR;
    }


    public static function makeOpen(): Status
    {
        return new static(static::OPEN);
    }

    public static function makeClose(): Status
    {
        return new static(static::CLOSE);
    }

    public static function makeError(): Status
    {
        return new static(static::ERROR);
    }
}