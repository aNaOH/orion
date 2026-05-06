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
        return "emails/register.twig";
    }

    protected function getPreheader(): string
    {
        return "Gracias por unirte a Orion. Estamos encantados de tenerte con nosotros.";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => $this->user->username,
            "user_email" => $this->user->email ?? $this->to,
        ];
    }
}
