<?php

namespace App\Http\Resources\Listing;

use App\Http\Resources\Base\BaseCollection;
use Illuminate\Http\Request;

class ListingCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    /**
     * The resource that this resource collects.
     */
    public $collects = ListingResource::class;
}
