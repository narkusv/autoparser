<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class New_classified extends Model
{
  protected $fillable = [
      'user_id', 'classified_id', 'parse_url_id',
  ];
}
