<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;

use Auth;

use Wetcat\Litterbox\Models\Country;
use Wetcat\Litterbox\Models\Order;
use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\Manufacturer;

use Unirest\Request as Unirequest;

use Wetcat\Fortie\Fortie;

use Ramsey\Uuid\Uuid;

class PagesController extends Controller {

  protected $fortie;

  public function __construct (Fortie $fortie)
  {
    $this->fortie = $fortie;
  }

  private function hasNewOrders () {
    $orders = Order::with('handledBy')->get();
    return (!!$orders && count($orders) > 0);
  }

  private function hasNewCustomers () {
    $customers = Customer::with('verifiedBy')->get();
    $unVerified = [];
    foreach ($customers as $value) {
      $customer = $value->toArray();
      if ($customer['verified_by'] === null) {
        $unVerified[] = $value;
      }
    }
    return (!!$unVerified && count($unVerified) > 0);
  }

  private function hasCustomers () {
    $customers = Customer::all();
    return (!!$customers && count($customers) > 0);
  }

  private function hasManufacturers () {
    $manufacturers = Manufacturer::all();
    return (!!$manufacturers && count($manufacturers) > 0);
  }

  public function home()
  {
    $invoices = $this->fortie->invoices()->all();

    return view('pages.home')
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers())
      ->with('invoices', $invoices);
  }

  public function stock()
  {
    return view('pages.stock')
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

  public function manufacturers()
  {
    return view('pages.manufacturers')
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

  public function customers()
  {
    return view('pages.customers')
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

  public function deliveries()
  {
    return view('pages.deliveries')
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

  public function currencies()
  {
    return view('pages.currencies')
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

  public function batches()
  {
    return view('pages.batches')
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

  public function profile()
  {
    $countries = Country::where('enabled', 1)->get(['iso']);
    
    if (count($countries) > 0) {
      $codeArr = [];
      foreach ($countries as $country) {
        $codeArr[] = $country->iso;
      }
      
      $urlext = implode(';', $codeArr);


      $response = Unirequest::get('https://restcountries-v1.p.mashape.com/alpha/?codes='.$urlext,
        array(
          'X-Mashape-Key' => env('MASHAPE', ''),
          'Accept' => 'application/json'
        )
      );
    
      $countriesAssoc = [];
      foreach ($response->body as $key => $value) {
        $countriesAssoc[$value->alpha2Code] = $value;
      }

      return view('pages.profile')
        ->with('enabled', $countriesAssoc)
        ->with('hasNewOrders', $this->hasNewOrders())
        ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
    }

    return view('pages.profile')
      ->with('enabled', [])
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

  public function settings()
  {
    $response = Unirequest::get('https://restcountries-v1.p.mashape.com/all',
      array(
        'X-Mashape-Key' => env('MASHAPE', ''),
        'Accept' => 'application/json'
      )
    );

    $countries = Country::where('enabled', 1)->get();
    $enabled = [];
    foreach ($countries as $key => $value) {
      $enabled[] = $value->iso;
    }
    
    return view('pages.settings')
      ->with('countries', $response->body)
      ->with('enabled', $enabled)
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

  public function orders()
  {
    return view('pages.orders')
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

  public function economy()
  {
    return view('pages.economy')
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

  public function supply()
  {
    return view('pages.supply')
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

  public function campaigns()
  {
    return view('pages.campaigns')
      ->with('hasNewOrders', $this->hasNewOrders())
      ->with('hasNewCustomers', $this->hasNewCustomers())
      ->with('hasCustomers', $this->hasCustomers())
      ->with('hasManufacturers', $this->hasManufacturers());
  }

}
