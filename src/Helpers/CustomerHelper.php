<?php namespace Wetcat\Litterbox\Helpers;

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

class CustomerHelper {

  public static function createCustomerNumber ($customerName)
  {
    // Get random number based on time
    $randomTimeNumber = mt_rand(100, 999) . intval(microtime(true) * 10);

    // Get the combined ASCII value of the customer name
    $customerNameVal = 0;
    foreach (str_split($customerName) as $char) {
      $customerNameVal += intval(ord($char));
    }

    // Get the resulting base64 encoded customer number
    $customerNumber = base_convert(($randomTimeNumber + $customerNameVal), 10, 36);
    
    return $customerNumber;
  } 

}
