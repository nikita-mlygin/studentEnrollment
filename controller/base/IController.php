<?php

namespace App\Base\Controller;

interface IController
{
    function runAction(string $actionName);
    function runDefault();
}