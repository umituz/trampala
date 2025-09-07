<?php

namespace App\Http\Resources\Admin\Category;

use App\Http\Resources\Base\BaseCollection;

class CategoryCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    /**
     * The resource that this resource collects.
     */
    public $collects = CategoryResource::class;
}
