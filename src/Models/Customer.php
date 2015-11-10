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

class Customer extends \Vinelab\NeoEloquent\Eloquent\Model  {

  use SoftDeletes;

	protected $label = 'Customer';

	protected $fillable = [
    'uuid', 'name', 'number', 'corporate', 
    'store_type', 
    'invoice_type_paper', 'invoice_type_email',
    'rebate',
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

  public function verifiedBy()
  {
    return $this->hasOne('Wetcat\Litterbox\Models\User', 'VERIFIED_BY');
  }
  

  public function chain()
  {
    return $this->belongsTo('Wetcat\Litterbox\Models\Chain', 'HAS_MEMBER');
  }

  public function segment()
  {
    return $this->belongsTo('Wetcat\Litterbox\Models\Customersegment', 'HAS_CUSTOMER');
  }

  public function chainsegment()
  {
    return $this->belongsTo('Wetcat\Litterbox\Models\Chainsegment', 'HAS_CUSTOMER');
  }

  public function addresses()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Address', 'HAS_ADDRESS');
  }

  public function orders()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Order', 'HAS_ORDER');
  }

  public function emails()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Email', 'HAS_EMAIL');
  }

  public function users()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\User', 'HAS_USER');
  }

  public function picture()
  {
    return $this->hasOne('Wetcat\Litterbox\Models\Picture', 'HAS_PICTURE');
  }

  public function articles()
  {
    return $this->hasMany('Wetcat\Litterbox\Models\Article', 'HAS_ARTICLE');
  }

}