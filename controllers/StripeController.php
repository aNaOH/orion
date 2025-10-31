<?php

use Stripe\Product;
use Stripe\Price;

class StripeController
{
    private static function getStripe()
    {
        return new \Stripe\StripeClient([
            "api_key" => $_ENV["STRIPE_KEY"],
            "stripe_version" => "2025-10-29.clover",
        ]);
    }

    private static function getGoToText($goTo)
    {
        return strlen(trim($goTo)) == 0 ? "" : "?goTo=" . urlencode($goTo);
    }

    private static function getProductWithPrice($game)
    {
        $productQuery = Product::search([
            "query" => 'name:\'' . $game->title . '\'',
        ]);

        if (count($productQuery->data) == 0) {
            $product = Product::create([
                "name" => $game->title,
                "description" => $game->description,
                "metadata" => [
                    "game" => $game->id,
                ],
            ]);
        } else {
            $product = $productQuery->data[0];
        }

        $price = Price::create([
            "unit_amount" => $game->base_price * 100,
            "currency" => "eur",
            "product" => $product->id,
        ]);

        $coupon = null;

        if (
            !is_null($game->discount) &&
            is_float($game->discount) &&
            $game->discount > 0
        ) {
            $coupon = \Stripe\Coupon::create([
                "percent_off" => $game->discount * 100, // Descuento
                "duration" => "once",
                "applies_to" => [
                    "products" => [$product->id],
                ],
            ]);
        }

        return [
            "price" => $price->id,
            "coupon" => $coupon?->id,
        ];
    }

    public static function createOrder($user)
    {
        \Stripe\Stripe::setApiKey($_ENV["STRIPE_KEY"]);

        $gamesOnCart = OrderHelper::getInstances();

        $line_items = [];
        $coupons = [];
        foreach ($gamesOnCart as $game) {
            $stripeContent = self::getProductWithPrice($game);
            $line_items[] = [
                "price" => $stripeContent["price"],
                "quantity" => 1,
            ];
            if (isset($stripeContent["coupon"])) {
                $coupons[] = $stripeContent["coupon"];
            }
        }

        $checkout_session_array = [
            "customer_email" => $user->email,
            "billing_address_collection" => "required",
            "line_items" => $line_items,
            "mode" => "payment",
            "automatic_tax" => [
                "enabled" => true,
            ],
            "metadata" => [
                "user" => $user->id,
            ],
            "ui_mode" => "custom",
            "payment_method_types" => ["card"],
            "return_url" => "https://example.com/checkout/success",
        ];

        if (count($coupons)) {
            $checkout_session_array["discounts"] = [
                [
                    "coupon" => $coupons[0],
                ],
            ];
        }

        $checkout_session = self::getStripe()->checkout->sessions->create(
            $checkout_session_array,
        );

        header("HTTP/1.1 200 OK");

        $response["status"] = "200";
        $response["message"] = "Checkout session created successfully";
        $response["client_secret"] = $checkout_session->client_secret;

        echo json_encode($response);
    }

    public static function success()
    {
        if (isset($_GET["from"])) {
            if ($_GET["from"] == "developer") {
                include "views/stripe/success/dev.php";
                exit();
            }

            if (preg_match('/^game(\d+)$/', $_GET["from"], $matches)) {
                $gameId = $matches[1];
                $user = User::getById($_SESSION["user"]["id"]);

                $game = Game::getById($gameId);

                if (is_null($game)) {
                    return false;
                }

                if (!$user->hasAdquiredGame($game, false, $checkoutId)) {
                    return false;
                }

                \Stripe\Stripe::setApiKey($_ENV["STRIPE_KEY"]);

                $checkout_session = \Stripe\Checkout\Session::retrieve(
                    $checkoutId,
                );

                $price = $checkout_session->amount_subtotal;
                $discountedAmount =
                    $checkout_session->total_details->amount_discount ?? 0;

                if ($price > 0) {
                    $discount = intval(($discountedAmount / $price) * 100);
                }

                $GLOBALS["game"] = $game;
                $GLOBALS["checkoutinfo"] = [
                    "price" => $price / 100,
                    "discountedAmount" => $discountedAmount / 100,
                    "discount" => $discount ?? 0,
                ];

                include "views/stripe/success/game.php";
                exit();
            }
        }

        include "views/stripe/success.php";
    }

    public static function cancel() {}

    public static function webhook()
    {
        $endpoint_secret = $_ENV["STRIPE_WEBHOOK_SECRET"];

        $payload = @file_get_contents("php://input");
        $sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret,
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        if (
            $event->type == "checkout.session.completed" ||
            $event->type == "checkout.session.async_payment_succeeded"
        ) {
            self::fulfill_checkout($event->data->object->id);
        }

        http_response_code(200);
    }

    private static function fulfill_checkout($session_id)
    {
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        $stripe = self::getStripe();
        \Stripe\Stripe::setApiKey($_ENV["STRIPE_KEY"]);

        // TODO: Log the string "Fulfilling Checkout Session $session_id"

        // TODO: Make this function safe to run multiple times,
        // even concurrently, with the same session ID

        // TODO: Make sure fulfillment hasn't already been
        // peformed for this Checkout Session

        // Retrieve the Checkout Session from the API with line_items expanded
        $checkout_session = $stripe->checkout->sessions->retrieve($session_id, [
            "expand" => ["line_items"],
        ]);

        // Check the Checkout Session's payment_status property
        // to determine if fulfillment should be peformed
        if ($checkout_session->payment_status != "unpaid") {
            $line_items = $checkout_session->line_items;
            $price_id = $line_items->data[0]->price->id;
            $user_id = $checkout_session->metadata["user"];

            $user = User::getById($user_id);

            if ($price_id == $_ENV["STRIPE_DEVACCOUNT_PRICE"]) {
                $dev_name =
                    $checkout_session->metadata["developer"] ?? $user->username;
                $user->addDeveloperInfo($dev_name);
            } else {
                $maybeProduct = $line_items->data[0]->price->product;

                $product =
                    $maybeProduct instanceof Product
                        ? $maybeProduct
                        : Product::retrieve($maybeProduct);

                $game = Game::getById(intval($product->metadata["id"]));

                if (!is_null($game)) {
                    $user->adquireGame($game, $checkout_session->id);
                }
            }
        }
    }
}
