<?php
declare(strict_types=1);

namespace data;

interface IReadableEntity
{
    public static function createFromId(int $idUser);
}