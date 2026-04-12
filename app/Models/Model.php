<?php

namespace app\Models;
interface Model
{
    public function save(array $data, ?int $id = null);
    public function load(?int $id = null, bool $all = false): ?array;
    public function delete(int $id): bool;
}