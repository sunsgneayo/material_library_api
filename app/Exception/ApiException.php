<?php
declare(strict_types = 1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class ApiException extends ServerException
{
    public function __construct(int $code = 0, string $message = NULL, Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }

}
