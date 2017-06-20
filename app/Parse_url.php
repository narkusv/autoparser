<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parse_url extends Model
{

  protected $fillable = [
      'url',
  ];

  public function users()
  {
    return $this->belongsToMany('App\User', 'user_parse_url')->withPivot('name');;
  }

  public function classifieds()
   {
       return $this->hasMany('App\Classified', 'parse_url_id');
   }
}
