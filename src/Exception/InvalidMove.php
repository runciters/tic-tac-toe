<?php

namespace App\Exception;

class InvalidMove extends \DomainException
{
    protected $message = "This move is not valid";
}