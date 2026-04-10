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
        return "¡Ahora eres un desarrollador!";
    }

    protected function getTemplatePath(): string
    {
        return "emails/developer_welcome.twig";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => $this->user->username,
            "developer_name" => $this->developer->name,
        ];
    }
}
