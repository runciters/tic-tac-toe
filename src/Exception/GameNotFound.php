<?php

namespace App\Exception;

class GameNotFound extends \DomainException
{
    protected $message = "Game not found";
}