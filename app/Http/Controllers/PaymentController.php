<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Paystack;

class PaymentController extends Controller
{



    public function redirectToGateway()
    {
        return Paystack::getAuthorizationUrl()->redirectNow();
    }

    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();
        
                     $curl = curl_init();
                    $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
                    if(!$reference){
                      die('No reference supplied');
                    }
                    
                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_SSL_VERIFYHOST => false,
                      CURLOPT_SSL_VERIFYPEER => false,
                      
                      CURLOPT_HTTPHEADER => [
                        "accept: application/json",
                        // "authorization: Bearer sk_live_a6a7ba6dc6fdd5fed5d6490a51d52b693191026e",
                        "authorization: Bearer sk_test_c388499eac2200cf3bfe64dd23315023fef090cb",
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
                    
                    if(!$tranx->status){
                      // there was an error from the API
                      die('API returned error: ' . $tranx->message);
                    }
                    
                    if('success' == $tranx->data->status){
                      // transaction was successful...
                      // please check other things like whether you already gave value for this ref
                      // if the email matches the customer who owns the product etc
                      // Give value
                      $shipping_details= DB::table('tbl_shipping')
                 ->where('shipping_id', Session::get('shipping_id'))
                 ->get();
                  $product= DB::table('tbl_products')
                              ->where('product_id', Session::get('product_id'))
                              ->get();
                  $product = json_decode($product);
                  $shipping_details = json_decode($shipping_details);
                  $data=array();
                  $data['name'] = $shipping_details[0]->shipping_first_name.' '.$shipping_details[0]->shipping_last_name;
                  $data['address'] = $shipping_details[0]->shipping_address;
                  $data['phone'] = $shipping_details[0]->shipping_mobile_number;
                  $data['email'] = $shipping_details[0]->shipping_email;
                  $data['order_id'] = $tranx->data->id;
                  $data['product_id'] = $product[0]->product_id;
                  $data['product_name'] = $product[0]->product_name;
                  $data['total_including_vat'] = Session::get('total_price');
                  $data['product_price'] = $product[0]->product_price;
                  $data['product_sales_quantity'] = Session::get('qty');
                  $data['status'] = 'pending';

                  $initial_sold = $product[0]->sold;
                  $new_sold = $initial_sold + Session::get('qty');
                  DB::update('update  tbl_products set sold ='.$new_sold.' where product_id  = ?', [$data['product_id']]);

                  DB::table('tbl_order_details')
                          ->insertGetId($data);
                  return view('callback');
                    }

       // dd($paymentDetails);

    }



}
