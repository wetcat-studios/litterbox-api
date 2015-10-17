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

class Order extends \Vinelab\NeoEloquent\Eloquent\Model  {

  use SoftDeletes;

	protected $label = 'Order';

	protected $fillable = [
    'uuid', 'number', 'numArticles',
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

  public function customer()
  {
    return $this->belongsTo('Wetcat\Litterbox\Models\Customer', 'HAS_ORDER');
  }

  public function articles()
  {
    return $this->belongsToMany('Wetcat\Litterbox\Models\Article', 'HAS_ORDER');
  }

  public function handledBy()
  {
    return $this->hasOne('Wetcat\Litterbox\Models\User', 'HANDLED_BY');
  }

}