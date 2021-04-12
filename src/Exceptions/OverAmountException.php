<?php

namespace Gabievi\Promocodes\Exceptions;

use Exception;

class OverAmountException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'The limit to use the promotional code has been completed. ';

    /**
     * @var int
     */
    protected $code = 403;
}
