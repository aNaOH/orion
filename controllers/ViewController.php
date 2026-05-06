<?php

/**
 * ViewController — Renders Twig templates with base data injection.
 * Singleton pattern for the Twig environment to avoid re-initialization.
 */
class ViewController
{
    private static ?\Twig\Environment $twig = null;

    /**
     * Get or create the Twig environment singleton.
     */
    private static function getTwig(): \Twig\Environment
    {
        if (self::$twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader('./views');
            self::$twig = new \Twig\Environment($loader, [
                'cache' => './cache/twig',
                'debug' => true,
            ]);

            // Debug extension
            self::$twig->addExtension(new \Twig\Extension\DebugExtension());

            // Custom functions

            // asset('/css/main.css') → '/assets/css/main.css'
            self::$twig->addFunction(new \Twig\TwigFunction('asset', function (string $path): string {
                return '/assets/' . ltrim($path, '/');
            }));

            // cdn_url('game/icon', id) → 'https://cdn.orion.moonnastd.com/game/icon/{id}'
            self::$twig->addFunction(new \Twig\TwigFunction('cdn_url', function (string $type, $id): string {
                return 'https://cdn.orion.moonnastd.com/' . $type . '/' . $id;
            }));

            // profile_pic(user) → user->getProfilePicURL()
            self::$twig->addFunction(new \Twig\TwigFunction('profile_pic', function ($user): string {
                return $user->getProfilePicURL();
            }));

            // snapshot_pic(report) → snapshot URL
            self::$twig->addFunction(new \Twig\TwigFunction('snapshot_pic', function ($report): string {
                $snapshot = $report->snapshot;
                $filename = $snapshot['profile_pic'] ?? 'default.png';
                
                // If it starts with 'snapshot_', it belongs to the official snapshots folder
                if (strpos($filename, 'snapshot_') === 0) {
                    return 'https://cdn.orion.moonnastd.com/tickets/snapshots/' . $filename;
                }
                
                // Otherwise, it was the default picture at the time
                return 'https://cdn.orion.moonnastd.com/user/profile_pic/' . $filename;
            }));

            // user_handle(user) → user->getHandle()
            self::$twig->addFunction(new \Twig\TwigFunction('user_handle', function ($user): string {
                return $user->getHandle();
            }));

            // token_input('type', params) → generates <input type="hidden" ...>
            self::$twig->addFunction(new \Twig\TwigFunction('token_input', function (string $type, array $params = []): string {
                $token = "";
                switch (strtoupper($type)) {
                    case 'COMMON':
                        $token = Token::createToken();
                        break;
                    case 'AUTHFORM':
                        $token = AuthFormToken::createToken();
                        break;
                    case 'USERACTION':
                        $token = UserActionToken::createToken();
                        break;
                    case 'DEVACTION':
                        $token = DevActionToken::createToken($params["userID"], $params["gameID"]);
                        break;
                }
                return '<input type="hidden" name="tript_token" id="tript_token" value="' . $token . '">';
            }, ['is_safe' => ['html']]));

            // csrf_token('type', params) → Alias for token_input
            self::$twig->addFunction(new \Twig\TwigFunction('csrf_token', function (string $type, array $params = []): string {
                $token = "";
                switch (strtoupper($type)) {
                    case 'COMMON': $token = Token::createToken(); break;
                    case 'AUTHFORM': $token = AuthFormToken::createToken(); break;
                    case 'USERACTION': $token = UserActionToken::createToken(); break;
                    case 'DEVACTION': $token = DevActionToken::createToken($params["userID"], $params["gameID"]); break;
                }
                return '<input type="hidden" name="tript_token" id="tript_token" value="' . $token . '">';
            }, ['is_safe' => ['html']]));

            // asset_scripts('group') → Returns script tags for grouped assets
            self::$twig->addFunction(new \Twig\TwigFunction('asset_scripts', function (string $group): string {
                $scripts = [];
                switch ($group) {
                    case 'admin-table':
                        $scripts[] = '<script src="/assets/js/orion-panel/table-tooltip.js"></script>';
                        $scripts[] = '<script src="/assets/js/orion-panel/delete-popup.js"></script>';
                        $scripts[] = '<script src="/assets/js/components/gradientSquare.js"></script>';
                        $scripts[] = '<script src="/assets/js/components/gradientChip.js"></script>';
                        break;
                }
                return implode("\n", $scripts);
            }, ['is_safe' => ['html']]));

            // admin_delete_setup(api_url, redirect_url, title) → Standard delete script generator
            self::$twig->addFunction(new \Twig\TwigFunction('admin_delete_setup', function (string $apiUrl, string $redirectUrl, string $title = '¿Eliminar registro?'): string {
                return "
                <script>
                  setupDeletePopup({
                    selector: '.delete-btn',
                    getName: (btn) => btn.dataset.name,
                    getDeleteUrl: (btn) => `{$apiUrl}`.replace('{id}', btn.dataset.id),
                    title: '{$title}',
                    onConfirm: (url) => {
                      const token = document.getElementById('tript_token')?.value;
                      const separator = url.includes('?') ? '&' : '?';
                      fetch(url + separator + 'tript_token=' + token, { method: 'DELETE' })
                        .then(response => {
                          if (response.ok) {
                            window.location.href = '{$redirectUrl}';
                          } else {
                            console.error('Error al eliminar');
                          }
                        })
                        .catch(error => console.error('Error al eliminar:', error));
                    }
                  });
                </script>";
            }, ['is_safe' => ['html']]));

            // markdown filter using TailwindParsedown
            self::$twig->addFilter(new \Twig\TwigFilter('markdown', function (string $content): string {
                $parsedown = new TailwindParsedown();
                return $parsedown->text($content);
            }, ['is_safe' => ['html']]));
        }

        return self::$twig;
    }

    /**
     * Render a Twig view.
     *
     * @param string $view View name without extension (e.g. 'legal/cookies')
     * @param array  $data Data to pass to the template
     */
    public static function render(string $view, array $data = []): void
    {
        $twig = self::getTwig();
        echo $twig->render($view . '.twig', array_merge(ViewHelpers::getBaseData(), $data));
    }

    /**
     * Render a Twig view and exit. Useful for controllers/routes.
     *
     * @param string $view View name without extension
     * @param array  $data Data to pass to the template
     */
    public static function renderFromController(string $view, array $data = []): void
    {
        self::render($view, $data);
        exit();
    }
}