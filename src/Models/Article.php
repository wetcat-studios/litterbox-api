<?php namespace Wetcat\Litterbox\Models;

/*

   Copyright 2015 Andreas Göransson

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

*/

use Vinelab\NeoEloquent\Eloquent\SoftDeletes;

class Article extends \NeoEloquent {

  use SoftDeletes;

	protected $label = 'Article';

	protected $fillable = [
    'uuid', 'name', 'number', 'ean', 'price_in', 'discountrate', 'restock_threshold', 'restock_amount', 'expired', 
    'package_weight', 'package_width', 'package_length', 'package_height',
    'colli_weight', 'colli_width', 'colli_length', 'colli_height',
    'package_per_colli', 'colli_per_eu_pallet', 'colli_per_eu_lav', 'colli_per_half_pallet', 'colli_per_half_lav',
    'colli_per_ship_pallet', 'colli_per_ship_lav',
    'nutrients_energy', 'nutrients_fat', 'nutrients_saturatedfat', 'nutrients_carbs', 'nutrients_sugar', 
    'nutrients_fibers', 'nutrients_protein', 'nutrients_salt',
	];
  
  protected $hidden = [
  ];

  protected $dates = [
    'deleted_at'
  ];

  public function createdBy()
  {
    return $this->hasOne('Wetcat\Litterbox\Models\User', 'CREATED_BY');
  }

  public function deletedBy()
  {
    return $this->hasOne('Wetcat\Litterbox\Models\User', 'DELETED_BY');
  }

  public function categories()
  {
    return $this->belongsToMany('Wetcat\Litterbox\Models\Category', 'HAS_ARTICLE');
  }

  public function segment()
  {
    return $this->belongsTo('Wetcat\Litterbox\Models\Segment', 'HAS_ARTICLE');
  }

  public function brand()
  {
    return $this->belongsTo('Wetcat\Litterbox\Models\Brand', 'HAS_ARTICLE');
  }

  public function manufacturer()
  {
    return $this->belongsTo('Wetcat\Litterbox\Models\Manufacturer', 'HAS_ARTICLE');
  }

  public function batches()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Batch', 'HAS_BATCH');
  }

  public function orders()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Order', 'HAS_ORDER');
  }

  public function pictures()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Picture', 'HAS_PICTURE');
  }

  public function ingredients()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Ingredient', 'HAS_INGREDIENT');
  }

  public function restocks()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Restock', 'HAS_RESTOCKS');
  }

  public function campaigns()
  {
    return $this->belongsToMany('Wetcat\Litterbox\Models\Campaign', 'HAS_CAMPAIGN');
  }
  
  // Helper methods for calculating the amount of items in stock

  public function incomming ()
  {
    $stock = 0;

    foreach ($this->batches as $batch) {
      $edge = $batch->article()->edge($this);

      $stock = $stock + $edge->count;
    }

    return $stock;
  }

  public function outgoing ()
  {
    $stock = 0;

    foreach ($this->orders as $order) {
      $edge = $order->articles()->edge($this);

      $stock = $stock + $edge->count;
    }

    return $stock;
  }

  public function total ()
  {
    $inc = $this->incomming();
    $out = $this->outgoing();

    return $inc - $out;
  }

}