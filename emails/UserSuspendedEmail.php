<?php

require_once __DIR__ . "/Email.php";

class UserSuspendedEmail extends Email
{
    private User $user;
    private UserSuspension $suspension;

    public function __construct(string $to, User $user, UserSuspension $suspension)
    {
        $this->user = $user;
        $this->suspension = $suspension;
        parent::__construct($to);
    }

    protected function getSubject(): string
    {
        return $this->suspension->isIndefinite()
            ? "Tu cuenta de Orion ha sido suspendida"
            : "Tu cuenta de Orion ha sido suspendida temporalmente";
    }

    protected function getTemplatePath(): string
    {
        return "emails/user_suspended.twig";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => $this->user->username,
            "reason" => $this->suspension->reason,
            "admin_comment" => $this->suspension->admin_comment,
            "starts_at" => $this->suspension->starts_at,
            "ends_at" => $this->suspension->ends_at,
            "is_indefinite" => $this->suspension->isIndefinite(),
            "guidelines_url" => $this->emailUrl("/legal/community-guidelines"),
        ];
    }
}
