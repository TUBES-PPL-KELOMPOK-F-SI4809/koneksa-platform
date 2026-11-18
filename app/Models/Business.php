<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['business_category_id', 'name', 'description', 'address', 'district', 'subdistrict', 'contact_phone'])]
class Business extends Model
{
    //
}
