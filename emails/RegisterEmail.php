<?php

require_once __DIR__ . "/Email.php";

class RegisterEmail extends Email
{
    private $user;

    public function __construct(string $to, User $user)
    {
        $this->user = $user;
        parent::__construct($to);
    }

    protected function getSubject(): string
    {
        return "¡Bienvenido a Orion!";
    }

    protected function getTemplatePath(): string
    {
        return __DIR__ . "/templates/register_email.dmail";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => $this->user->username,
        ];
    }

    protected function getFonts(): string
    {
        return '<link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&family=Lexend:wght@100..900&display=swap" rel="stylesheet">';
    }
}
