<?php

use \Stripe\Product;
use \Stripe\Price;

class StripeController {
    public static function buy(Product|string $product, User $user, $metadata = []){
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_KEY']);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = $_ENV['STRIPE_DOMAIN'];

        if($product instanceof Product){
            $price_id = $product->default_price->id;
        } else {
            $price_id = $product;
        }

        $checkout_session = \Stripe\Checkout\Session::create([
        'customer_email' => $user->email,
        'billing_address_collection' => 'required',
        'line_items' => [[
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            'price' => $price_id,
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/stripe/success',
        'cancel_url' => $YOUR_DOMAIN . '/stripe/cancel',
        'automatic_tax' => [
            'enabled' => true,
        ],
        'metadata' => $metadata
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
    }

    public static function success(){
        var_dump($_POST);
    }

    public static function cancel(){
        
    }
}