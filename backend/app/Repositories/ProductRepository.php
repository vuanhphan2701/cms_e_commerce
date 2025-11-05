<?php

namespace App\Models;

use App\Models\Product;
use Core\Repositories\BaseRepository;

class ProductRepository extends BaseRepository
{
    protected $model = Product::class;


}
