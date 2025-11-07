<?php

namespace App\Repositories;

use App\Models\Product;
use Core\Repositories\BaseRepository;

class ProductRepository extends BaseRepository
{
    protected string $model = Product::class;


}
