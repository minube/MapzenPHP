<?php

namespace Mapzen\Exceptions;

class ClientException extends AbstractException
{
    /** @var int */
    protected $code = 503;

    /** @var string */
    protected $message = "Mapzen Client Error";
}
