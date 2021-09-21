<?php
declare(strict_types=1);

namespace data;

interface IWritableEntity extends IReadableEntity
{
    public function registerOrUpdate(): void;

    public function delete(): void;
}