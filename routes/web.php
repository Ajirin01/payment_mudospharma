<?php
Route::get('/', function (Request $request) {
      // return response()->json($request::get('customer_id'));
        $temp_data = DB::table('tbl_temp_data')
                      ->where('customer_id',$request::get('customer_id'))
                      ->first();
        $customer = DB::table('tbl_customer')
                      ->where('customer_id',$request::get('customer_id'))
                      ->first();
                      
        // return response()->json($temp_data->total_price);
        // exit;
    return view('welcome',['total_price'=>$temp_data->total_price,
    'name'=>$customer->customer_name,
    'email'=>$customer->customer_email
    ]);
});


use App\Http\Requests;

Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay');
//for testing purpose
// Route::post('/pay', function(Request $request){
//     $url = "https://api.paystack.co/transaction/initialize";
//   $fields = [
//     // 'email' => "customer@email.com",
//     // 'amount' => "20000",
//     'email' => "customer@email.com",
//     'amount' => "20000",
//   ];
//   $fields_string = http_build_query($fields);
//   //open connection
//   $ch = curl_init();
  
//   //set the url, number of POST vars, POST data
//   curl_setopt($ch,CURLOPT_URL, $url);
//   curl_setopt($ch,CURLOPT_POST, true);
  
//   curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
//   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//     "Authorization: Bearer sk_test_c388499eac2200cf3bfe64dd23315023fef090cb",
//     "Cache-Control: no-cache",
//   ));
  
//   //So that curl_exec returns the contents of the cURL; rather than echoing it
//   curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
//   curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false); 
//   curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false); 
//   //execute post
//   $result = curl_exec($ch);
  
//   $response = json_decode($result);
//   $payment_gatway_url = $response->data->authorization_url;
//   return redirect($payment_gatway_url);
// })->name('pay');

// Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');
Route::get('/payment/callback', function(){
    $curl = curl_init();
    $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
    if(!$reference){
      die('No reference supplied');
    }
    $public_key = "Bearer ".env('PAYSTACK_SECRET_KEY');
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      
      CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "authorization: $public_key",
        "cache-control: no-cache"
      ],
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    if($err){
        // there was an error contacting the Paystack API
      die('Curl returned error: ' . $err);
    }
    
    $tranx = json_decode($response);
    // echo json_encode($tranx->data->customer->email);
    // exit;
    if(!$tranx->status){
      // there was an error from the API
      die('API returned error: ' . $tranx->message);
    }
    
    if('success' == $tranx->data->status){

    
    
    // $customer_id = DB::table('tbl_customer')
    //              ->where('customer_email', $tranx->data->customer->email)
    //              ->first();

    $customer_id = DB::table('tbl_customer')
                 ->where('customer_email', 'mubarakolagoke@gmail.com')
                 ->first();
    
    $temp_data= DB::table('tbl_temp_data')
               ->where('customer_id', $customer_id->customer_id)
               ->first();
    $shipping_details= DB::table('tbl_shipping')
                 ->where('shipping_id', $temp_data->shipping_id)
                 ->first();

    $products= $temp_data->cart;
    $products = json_decode($products);

    $data=array();
    $data['name'] = $shipping_details->shipping_first_name.' '.$shipping_details->shipping_last_name;
    $data['address'] = $shipping_details->shipping_address;
    $data['phone'] = $shipping_details->shipping_mobile_number;
    $data['email'] = $shipping_details->shipping_email;
    $data['order_id'] = $tranx->data->id;
    $data['order_details'] = $temp_data->cart;
    $data['order_total'] = $temp_data->total_price;
    $data['status'] = 'pending';

    foreach ($products as $product) {
      $table_product = DB::table('tbl_products')
                 ->where('product_id', $product->id)
                 ->first();

      $initial_sold = $table_product->sold;
      $new_sold = $initial_sold + $product->quantity;

      DB::update('update  tbl_products set sold ='.$new_sold.' where product_id  = ?', [$product->id]);
      // exit;
    }

    DB::table('tbl_order_details')
            ->insert($data);
    return view('callback');
      
    }

});

Route::get('/check-database', function(){
    // $shipping_details= DB::table('tbl_shipping')
    // ->where('shipping_id', Session::get('shipping_id'))
    // ->get();

    // return response()->json($shipping_details);

    $temp_data= DB::table('tbl_temp_data')
               ->where('customer_id', 1)
               ->first();
    $products= $temp_data->cart;
    $products = json_decode($products);

    //   foreach ($products as $product) {
    //   echo $product->id;
    // }
    return response()->json($products);
});