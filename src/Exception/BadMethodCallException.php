<?php
declare(strict_types=1);

namespace Application\Exception;

class BadMethodCallException extends \BadMethodCallException implements ExceptionInterface
{
}
