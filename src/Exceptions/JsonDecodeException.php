<?php


namespace KubanOnline\Exceptions;


use Throwable;

class JsonDecodeException extends \RuntimeException
{
    private string $response;

    public function __construct(string $response, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    public function getResponse(): string
    {
        return $this->response;
    }
}