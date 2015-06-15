<?php
/**
 * Created by Igor on 15/06/2014
 */

namespace AppShed\Extensions\StorageBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class MissingDataException extends HttpException
{
    public function __construct($message)
    {
        parent::__construct(400, $message);
    }
} 