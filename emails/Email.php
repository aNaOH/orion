<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

abstract class Email
{
    protected string $to;
    protected string $subject;
    protected string $body;

    /** @var array<int, array> */
    protected array $attachments = [];

    public function __construct(string $to)
    {
        if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("Destinatario inválido: $to");
        }

        $this->to = $to;
        $this->subject = $this->getSubject();
        // Body will be rendered on send or via getHtml to ensure variables are set
    }

    abstract protected function getSubject(): string;
    abstract protected function getTemplatePath(): string;

    protected function getVariables(): array
    {
        return [];
    }

    /** Previsualizar HTML final */
    public function getHtml(): string
    {
        return $this->renderBody();
    }

    /** Resuelve el MIME type de un archivo intentando usar mime_content_type (fileinfo) con fallback manual */
    private function guessMime(string $filepath): string
    {
        if (function_exists('mime_content_type') && file_exists($filepath)) {
            $mime = @mime_content_type($filepath);
            if ($mime) {
                return $mime;
            }
        }

        $mimes = [
            'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png'  => 'image/png',  'gif'  => 'image/gif',
            'webp' => 'image/webp', 'svg'  => 'image/svg+xml',
            'ico'  => 'image/x-icon',
            'pdf'  => 'application/pdf',
            'txt'  => 'text/plain', 'html' => 'text/html',
            'css'  => 'text/css',   'js'   => 'application/javascript',
        ];
        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        return $mimes[$ext] ?? 'application/octet-stream';
    }

    /** Adjuntar imagen local con CID */
    public function emailAsset(string $path): string
    {
        $fullPath = __DIR__ . "/../" . ltrim($path, "/");
        if (!file_exists($fullPath)) {
            if (str_starts_with($path, "/assets/")) {
                $fullPath = __DIR__ . "/../" . ltrim($path, "/");
            } else {
                $fullPath = __DIR__ . "/../assets/" . ltrim($path, "/");
            }
        }

        if (!file_exists($fullPath)) {
            return $path;
        }

        $content = file_get_contents($fullPath);
        if ($content === false || $content === "") {
            return $path;
        }

        $filename = basename($fullPath) ?: "asset_" . uniqid();
        $cid      = "cid_" . md5($fullPath . microtime(true));
        $mime     = $this->guessMime($fullPath);

        $this->attachments[] = [
            "cid"      => $cid,
            "filename" => $filename,
            "content"  => $content,
            "mime"     => $mime,
            "inline"   => true,
        ];

        return "cid:$cid";
    }

    /** Adjuntar imagen remota con CID */
    public function emailOnlineAsset(string $url): string
    {
        if (!preg_match("#^https?://#", $url)) {
            $url = $this->emailUrl($url);
        }

        $content = @file_get_contents($url);

        if ($content === false || $content === "") {
            return $url;
        }

        $filename = basename(parse_url($url, PHP_URL_PATH)) ?: "image_" . uniqid() . ".jpg";
        $cid      = "cid_" . md5($url . microtime(true));
        $mime     = $this->guessMime($filename);

        $this->attachments[] = [
            "cid"      => $cid,
            "filename" => $filename,
            "content"  => $content,
            "mime"     => $mime,
            "inline"   => true,
        ];

        return "cid:$cid";
    }

    /** Genera URL absoluta */
    public function emailUrl(string $path): string
    {
        $base = $_ENV["APP_URL"] ?? "http://localhost";
        return rtrim($base, "/") . "/" . ltrim($path, "/");
    }

    /** Renderiza el template HTML usando Twig */
    protected function renderBody(): string
    {

        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
        $twig = new \Twig\Environment($loader, [
            'cache' => false, // No cache for emails to avoid issues with dynamic inline attachments
            'debug' => true,
        ]);

        // Add custom functions for emails
        $twig->addFunction(new \Twig\TwigFunction('asset', [$this, 'emailAsset']));
        $twig->addFunction(new \Twig\TwigFunction('onlineAsset', [$this, 'emailOnlineAsset']));
        $twig->addFunction(new \Twig\TwigFunction('url', [$this, 'emailUrl']));

        $template = $this->getTemplatePath();

        if (!str_ends_with($template, '.twig')) {
            $template .= '.twig';
        }

        try {
            return $twig->render($template, $this->getVariables());
        } catch (\Exception $e) {
            throw new RuntimeException("Error rendering email template [$template]: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
        }
    }

    /** Envío SMTP */
    public function send(): bool
    {
        $this->body = $this->renderBody();
        $this->validateBeforeSend();

        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV["SMTP_HOST"] ?? 'localhost';
            $smtpAuthEnabled = !isset($_ENV["SMTP_AUTH"]) || filter_var($_ENV["SMTP_AUTH"], FILTER_VALIDATE_BOOLEAN);
            $mail->SMTPAuth = $smtpAuthEnabled;
            if ($smtpAuthEnabled) {
                $mail->Username = $_ENV["SMTP_USER"] ?? '';
                $mail->Password = $_ENV["SMTP_PASSWORD"] ?? '';
            }
            $mail->Port = intval($_ENV["SMTP_PORT"] ?? 587);

            $secure = $_ENV["SMTP_SECURE"] ?? 'tls';
            if ($secure === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            // Remitente
            $mail->setFrom($_ENV["EMAIL_FROM"] ?? 'no-reply@orion.com', "Orion");

            // Destinatario
            $mail->addAddress($this->to);

            // Inline attachments (Images)
            foreach ($this->attachments as $att) {
                if ($att["inline"]) {
                    $mail->addStringEmbeddedImage(
                        $att["content"],
                        $att["cid"],
                        $att["filename"],
                        "base64",
                        $att["mime"],
                    );
                }
            }

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $this->subject;
            $mail->Body = $this->body;
            $mail->AltBody = strip_tags($this->body);

            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new RuntimeException(
                "Error enviando email: " . $e->getMessage(),
                0,
                $e,
            );
        }
    }

    protected function validateBeforeSend(): void
    {
        $required = [
            "SMTP_HOST",
            "SMTP_PORT",
            "EMAIL_FROM",
        ];

        $smtpAuthEnabled = !isset($_ENV["SMTP_AUTH"]) || filter_var($_ENV["SMTP_AUTH"], FILTER_VALIDATE_BOOLEAN);
        if ($smtpAuthEnabled) {
            $required[] = "SMTP_USER";
            $required[] = "SMTP_PASSWORD";
        }

        foreach ($required as $key) {
            if (empty($_ENV[$key])) {
                throw new RuntimeException("$key no definido en el entorno");
            }
        }

        if (!filter_var($this->to, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("Destinatario inválido");
        }

        if (trim($this->subject) === "") {
            throw new RuntimeException("Subject vacío");
        }

        if (trim($this->body) === "") {
            throw new RuntimeException("Body vacío");
        }
    }
}
