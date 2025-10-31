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
        return __DIR__ . "/templates/order_success_email.dmail";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => $this->user->username,
            "order" => $this->order,
        ];
    }

    protected function getFonts(): string
    {
        return '<link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&family=Lexend:wght@100..900&display=swap" rel="stylesheet">';
    }
}
