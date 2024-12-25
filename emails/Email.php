<?php

class Email {
    protected string $to;
    protected string $subject;
    protected string $body;
    protected string $headers;

    public function __construct(string $to, string $subject, string $body) {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->headers = "MIME-Version: 1.0" . "\r\n";
        $this->headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $this->headers .= "From: no-reply@togetheronorion.com" . "\r\n";
    }

    public function send(): bool {
        return mail($this->to, $this->subject, $this->body, $this->headers);
    }

    protected function formatBody(string $content): string {
        return "<html><body>{$content}</body></html>";
    }
}
