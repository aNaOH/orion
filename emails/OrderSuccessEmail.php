<?php

require_once __DIR__ . "/Email.php";

class OrderSuccessEmail extends Email
{
    private $user;
    private $order;

    public function __construct(string $to, User $user, array $order)
    {
        $this->user = $user;
        $this->order = $order;
        parent::__construct($to);
    }

    protected function getSubject(): string
    {
        return "¡Pedido realizado!";
    }

    protected function getTemplatePath(): string
    {
        return "emails/order_success.twig";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => $this->user->username,
            "order_id" => $this->order['id'] ?? 'N/A',
            "items" => $this->order['items'] ?? [],
            "total" => $this->order['total'] ?? '0.00',
        ];
    }
}
