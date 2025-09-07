<?php

namespace App\Http\Resources\Admin\City;

use App\Http\Resources\Base\BaseCollection;

class CityCollection extends BaseCollection
{
    public $collects = CityResource::class;
}
