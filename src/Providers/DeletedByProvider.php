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

class DeletedByProvider extends ServiceProvider
{
  /**
   * Bootstrap the application services.
   *
   * @return void
   */
  public function boot()
  { 
    
    Address::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Article::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Batch::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Brand::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Campaign::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Category::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Chain::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Chainsegment::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    City::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    County::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Country::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Currency::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Customer::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Customersegment::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Email::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Group::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Ingredient::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Manufacturer::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Order::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Phone::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Picture::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Pricelist::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Rate::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Restock::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Segment::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });

    Thumbnail::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });
    
    Intrastat::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
    });
/*
    User::deleted(function ($model) {
      $token = Request::header('X-Litterbox-Token');
      $secret = TokenHelper::getSecret($token);
      $user = User::where('token', $secret)->first();
      $rel = $model->deletedBy()->save($user);
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
