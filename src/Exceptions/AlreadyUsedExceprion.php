<?php

namespace Gabievi\Promocodes\Exceptions;

use Exception;

class AlreadyUsedExceprion extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Promotion code is already used by current user.';

    /**
     * @var int
     */
    protected $code = 403;
}
