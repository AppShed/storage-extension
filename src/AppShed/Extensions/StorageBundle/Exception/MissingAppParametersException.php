<?php
/**
 * Created by mcfedr on 05/05/2014 20:12
 */

namespace AppShed\Extensions\StorageBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class MissingAppParametersException extends HttpException
{
    public function __construct($message)
    {
        parent::__construct(400, $message);
    }
} 