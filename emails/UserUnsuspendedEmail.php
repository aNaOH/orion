<?php

require_once __DIR__ . "/Email.php";

class UserUnsuspendedEmail extends Email
{
    private User $user;
    private string $reason;

    public function __construct(string $to, User $user, string $reason)
    {
        $this->user = $user;
        $this->reason = $reason;
        parent::__construct($to);
    }

    protected function getSubject(): string
    {
        return "Tu cuenta de Orion ha sido reactivada";
    }

    protected function getTemplatePath(): string
    {
        return "emails/user_unsuspended.twig";
    }

    protected function getPreheader(): string
    {
        return "Buenas noticias, " . $this->user->username . ". Tu cuenta ha sido reactivada y ya puedes volver a Orion.";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => $this->user->username,
            "reason" => $this->reason,
            "guidelines_url" => $this->emailUrl("/legal/community-guidelines"),
        ];
    }
}
