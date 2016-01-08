<?php namespace Wetcat\Litterbox\Providers;

use Illuminate\Support\ServiceProvider;

use Wetcat\Litterbox\Models\Address;
use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Batch;
use Wetcat\Litterbox\Models\Brand;
use Wetcat\Litterbox\Models\Campaign;
use Wetcat\Litterbox\Models\Category;
use Wetcat\Litterbox\Models\Chain;
use Wetcat\Litterbox\Models\Chainsegment;
use Wetcat\Litterbox\Models\City;
use Wetcat\Litterbox\Models\County;
use Wetcat\Litterbox\Models\Country;
use Wetcat\Litterbox\Models\Currency;
use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\Customersegment;
use Wetcat\Litterbox\Models\Email;
use Wetcat\Litterbox\Models\Group;
use Wetcat\Litterbox\Models\Ingredient;
use Wetcat\Litterbox\Models\Intrastat;
use Wetcat\Litterbox\Models\Manufacturer;
use Wetcat\Litterbox\Models\Order;
use Wetcat\Litterbox\Models\Phone;
use Wetcat\Litterbox\Models\Picture;
use Wetcat\Litterbox\Models\Pricelist;
use Wetcat\Litterbox\Models\Rate;
use Wetcat\Litterbox\Models\Restock;
use Wetcat\Litterbox\Models\Segment;
use Wetcat\Litterbox\Models\Thumbnail;
use Wetcat\Litterbox\Models\User;

use Request;

use Wetcat\Litterbox\Auth\Token as TokenHelper;

class CreatedByProvider extends ServiceProvider
{
  /**
   * Bootstrap the application services.
   *
   * @return void
   */
  public function boot()
  { 
    
    Address::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Article::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Batch::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Brand::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Campaign::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Category::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Chain::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Chainsegment::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    City::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    County::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Country::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Currency::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Customer::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Customersegment::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Email::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Group::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Ingredient::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Manufacturer::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Order::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Phone::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Picture::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Pricelist::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Rate::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Restock::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Segment::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });

    Thumbnail::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });
    
    Intrastat::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
      
      \Analytics::trackEvent('Intrastat', 'created', $model->uuid, date());
    });
    
/*
    User::created(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->createdBy()->save($user);
    });
*/
  }

  /**
   * Register the application services.
   *
   * @return void
   */
  public function register()
  {
      //
  }
}
