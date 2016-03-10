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

class Article extends \Vinelab\NeoEloquent\Eloquent\Model  {

  use SoftDeletes;

	protected $label = 'Article';
      
	protected $fillable = [
    'uuid', 'name', 'articleNumber', 'ean', 'price', 'discountrate', 'restockthreshold', 'restockamount', 'filename', 'intrastat', 'description',
    'packageweight', 'packagewidth', 'packagelength', 'packageheight',
    'colliweight', 'colliwidth', 'collilength', 'colliheight',
    'packagepercolli', 'collipereupallet', 'collipereulav', 'colliperhalfpallet', 'colliperhalflav', 'collipershippallet', 'collipershiplav',
    'kj', 'kcal', 'fat', 'saturatedfat', 'carbs', 'sugar', 'fibers', 'proteins', 'salt',
	
    'productCost', 'unitPrice', 'salesPrice', 'calculatedMargin',
  ];
  
  protected $hidden = [
  ];

  protected $dates = [
    'deleted_at'
  ];

  public function createdBy ()
  {
    return $this->hasOne('Wetcat\Litterbox\Models\User', 'CREATED_BY');
  }

  public function deletedBy ()
  {
    return $this->hasOne('Wetcat\Litterbox\Models\User', 'DELETED_BY');
  }

  public function categories ()
  {
    return $this->belongsToMany('Wetcat\Litterbox\Models\Category', 'HAS_ARTICLE');
  }

  public function segment ()
  {
    return $this->belongsTo('Wetcat\Litterbox\Models\Segment', 'HAS_ARTICLE');
  }

  public function brand ()
  {
    return $this->belongsTo('Wetcat\Litterbox\Models\Brand', 'HAS_ARTICLE');
  }

  public function manufacturer ()
  {
    return $this->belongsTo('Wetcat\Litterbox\Models\Manufacturer', 'HAS_ARTICLE');
  }

  public function batches ()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Batch', 'HAS_BATCH');
  }

  public function orders ()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Order', 'HAS_ORDER');
  }

  public function pictures ()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Picture', 'HAS_PICTURE');
  }

  public function ingredients ()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Ingredient', 'HAS_INGREDIENT');
  }

  public function restocks ()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Restock', 'HAS_RESTOCKS');
  }

  public function campaigns ()
  {
    return $this->belongsToMany('Wetcat\Litterbox\Models\Campaign', 'HAS_CAMPAIGN');
  }

  public function customers ()
  {
    return $this->belongsToMany ('Wetcat\Litterbox\Models\Customer', 'HAS_ARTICLE');
  }
  
  public function intrastat ()
  {
    return $this->belongsTo('Wetcat\Litterbox\Models\Intrastat', 'HAS_ARTICLE');
  }
  
  public function prices ()
  {
    return $this->belongsToMany('Wetcat\Litterbox\Models\Pricelist', 'HAS_PRICE');
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