<?php

/**
 * ViewHelpers — Provides global base data for all Twig views.
 */
class ViewHelpers
{
    /**
     * Returns base data available to every Twig template.
     * Includes session user info, cart data, and role flags.
     *
     * @return array
     */
    public static function getBaseData(): array
    {
        $data = [
            'session_user' => $_SESSION['user'] ?? null,
            'user' => null,
            'is_admin' => false,
            'is_developer' => false,
            'has_order' => false,
            'cart_items' => [],
            'cart_total' => 0,
            'server_time' => date('c'),
            'server_timezone' => date('T'),
        ];

        // If user is logged in, fetch full user object and role flags
        if (isset($_SESSION['user']['id'])) {
            $user = User::getById($_SESSION['user']['id']);
            if ($user) {
                $data['user'] = $user;
                $data['is_admin'] = ($user->role == EUSER_TYPE::ADMIN);
                $data['is_developer'] = !is_null($user->getDeveloperInfo());

                // Cart data
                if (OrderHelper::getOrder()) {
                    $data['has_order'] = true;
                    $data['cart_items'] = OrderHelper::getInstances();
                    $data['cart_total'] = OrderHelper::getTotal();
                }
            }
        }

        return $data;
    }
}
