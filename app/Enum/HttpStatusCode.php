<?php

namespace App\Enum;

abstract class HttpStatusCode 
{
    public const OK = 200;
    public const CREATED = 201;
    public const UPDATED = 204;
    public const DELETED = 204;
    public const NOT_FOUND = 404;
    public const UNPROCESSABLE_ENTITY = 422;
    public const INTERNAL_ERROR = 500;
}