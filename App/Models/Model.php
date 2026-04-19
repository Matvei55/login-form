<?php

namespace App\Models;
interface Model
{
    public function save();
    public function load(?int $id = null, bool $all = false): ?array;
    public function delete();
}