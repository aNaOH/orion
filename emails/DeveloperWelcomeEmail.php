<?php

require_once __DIR__ . "/Email.php";

class DeveloperWelcomeEmail extends Email
{
    private $user;
    private $developer;

    public function __construct(string $to, User $user, Developer $developer)
    {
        $this->user = $user;
        $this->developer = $developer;
        parent::__construct($to);
    }

    protected function getSubject(): string
    {
        return "¡Bienvenido a Orion!";
    }

    protected function getTemplatePath(): string
    {
        return __DIR__ . "/templates/developer_welcome_email.dmail";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => $this->user->username,
            "developer_name" => $this->developer->name,
        ];
    }

    protected function getFonts(): string
    {
        return '<link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&family=Lexend:wght@100..900&display=swap" rel="stylesheet">';
    }
}
