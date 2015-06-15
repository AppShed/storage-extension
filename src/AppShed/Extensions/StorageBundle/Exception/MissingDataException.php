<?php
/**
 * Created by Igor on 15/06/2014
 */

namespace AppShed\Extensions\StorageBundle\Exception;


class MissingDataException extends JsonHttpException
{
    public function __construct($message)
    {
        parent::__construct(400, $message);
    }
} 