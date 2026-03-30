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

            // user_handle(user) → user->getHandle()
            self::$twig->addFunction(new \Twig\TwigFunction('user_handle', function ($user): string {
                return $user->getHandle();
            }));
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
}