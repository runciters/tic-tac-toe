<?php

namespace App\Exception;

class GameOver extends \DomainException
{
    protected $message = "This game is over";
}