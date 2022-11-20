<?php

namespace App\Exception;

class InvalidPlayer extends \DomainException
{
    protected $message = "We have player 1 and 2 only :(";
}