<?php

use \Stripe\Product;
use \Stripe\Price;

require_once 'models/User.php';
require_once 'models/Developer.php';

class StripeController {

    private static function getStripe(){
        return new \Stripe\StripeClient($_ENV['STRIPE_KEY']);
    }

    private static function getFromText($from){
        return ((strlen(trim($from)) == 0) ? '' : '?from='.urlencode($from));
    }

    public static function buy(Product|string $product, User $user, $metadata = [], $from = ""){
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_KEY']);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = $_ENV['STRIPE_DOMAIN'];

        if($product instanceof Product){
            $price_id = $product->default_price->id;
        } else {
            $price_id = $product;
        }

        $fromText = self::getFromText($from);

        $checkout_session = \Stripe\Checkout\Session::create([
        'customer_email' => $user->email,
        'billing_address_collection' => 'required',
        'line_items' => [[
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            'price' => $price_id,
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/stripe/success'.$fromText,
        'cancel_url' => $YOUR_DOMAIN . '/stripe/cancel'.$fromText,
        'automatic_tax' => [
            'enabled' => true,
        ],
        'metadata' => $metadata
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
    }

    public static function success(){
        if(isset($_GET['from'])){
            if($_GET['from'] == "developer"){
                include('views/stripe/success/dev.php');
                exit();
            }
        }

        include('views/stripe/success.php');
    }

    public static function cancel(){
        
    }

    public static function webhook(){
        $endpoint_secret = "whsec_e265df41506c078c36920b4c7f89be41259733aade6ffc9b1869f17ac7bb9b04";

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        if (
            $event->type == 'checkout.session.completed'
            || $event->type == 'checkout.session.async_payment_succeeded'
        ) {
            self::fulfill_checkout($event->data->object->id);
        }

        http_response_code(200);
    }

    private static function fulfill_checkout($session_id) {
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        $stripe = self::getStripe();
      
        // TODO: Log the string "Fulfilling Checkout Session $session_id"
      
        // TODO: Make this function safe to run multiple times,
        // even concurrently, with the same session ID
      
        // TODO: Make sure fulfillment hasn't already been
        // peformed for this Checkout Session
      
        // Retrieve the Checkout Session from the API with line_items expanded
        $checkout_session = $stripe->checkout->sessions->retrieve($session_id, [
          'expand' => ['line_items'],
        ]);
      
        // Check the Checkout Session's payment_status property
        // to determine if fulfillment should be peformed
        if ($checkout_session->payment_status != 'unpaid') {
          $price_id = $checkout_session->line_items->data[0]->price->id;
          $user_id = $checkout_session->metadata['user'];

          $user = User::getById($user_id);

          if($price_id == $_ENV['STRIPE_DEVACCOUNT_PRICE']){
            $dev_name = $checkout_session->metadata['developer'] ?? $user->username;
            $user->addDeveloperInfo($dev_name);
          }
        }
      }
}