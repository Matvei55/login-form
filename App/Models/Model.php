<?php

namespace App\Models;
interface Model
{
    public function save();
    public function load(?int $id = null): self;
    public function delete();
}