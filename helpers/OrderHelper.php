<?php

class OrderHelper
{
    /**
     * Inicia un pedido en la sesión
     */
    public static function beginOrder()
    {
        $_SESSION["order"] = [
            "items" => [],
        ];
    }
    /**
     * Obtiene el pedido actual de la sesión
     * @return array|null
     */
    public static function getOrder()
    {
        if (!isset($_SESSION["order"])) {
            return null;
        }

        $order = $_SESSION["order"];
        if (!$order || !isset($order["items"])) {
            return null;
        }

        return $order;
    }

    public static function addItem(Game|int $game)
    {
        if (!isset($_SESSION["order"])) {
            return false;
        }
        $order = $_SESSION["order"];
        if (!$order || !isset($order["items"])) {
            return false;
        }

        $gameId = $game instanceof Game ? $game->id : $game;

        // Check if the game already exists in the order
        if (self::hasItem($gameId)) {
            return false;
        }
        // If not found, add new item
        $game = Game::getById($gameId);
        if ($game == null) {
            return false;
        }
        $order["items"][] = [
            "game_id" => $gameId,
            "game_snapshot" => [
                "id" => $game->id,
                "name" => $game->title,
                "base_price" => $game->base_price,
                "discount" => $game->discount,
            ],
        ];
        $_SESSION["order"] = $order;
        return true;
    }

    public static function removeItem($gameId)
    {
        $order = self::getOrder();
        if (!$order) {
            return false;
        }
        // Check if the game exists in the order
        foreach ($order["items"] as $key => &$item) {
            if ($item["game_id"] == $gameId) {
                unset($order["items"][$key]);

                if (count($order["items"]) == 0) {
                    self::clearOrder();
                } else {
                    $_SESSION["order"] = $order;
                }
                return true;
            }
        }
        return false;
    }

    public static function clearOrder()
    {
        if (self::getOrder() !== null) {
            unset($_SESSION["order"]);
        }
    }

    public static function hasItem($gameId)
    {
        if (!isset($_SESSION["order"]) || !isset($_SESSION["order"]["items"])) {
            return false;
        }
        foreach ($_SESSION["order"]["items"] as $item) {
            if ($item["game_id"] == $gameId) {
                return true;
            }
        }
        return false;
    }

    public static function getInstances()
    {
        $instances = [];
        if (!isset($_SESSION["order"]) || !isset($_SESSION["order"]["items"])) {
            return $instances;
        }
        foreach ($_SESSION["order"]["items"] as $item) {
            $instances[] = Game::getById($item["game_id"]);
        }
        return $instances;
    }

    public static function getTotal()
    {
        $games = self::getInstances();
        $total = 0;
        foreach ($games as $game) {
            $total += round(
                $game->base_price - $game->base_price * $game->discount,
                2,
            );
        }
        return $total;
    }
}
