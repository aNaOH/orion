<?php

require_once "controllers/StorefrontController.php";

$router->get('/legal', 'StorefrontController::showTerms');
$router->get('/legal/terms', 'StorefrontController::showTerms');
$router->get('/legal/privacy', 'StorefrontController::showPrivacy');
$router->get('/legal/cookies', 'StorefrontController::showCookies');
$router->get('/legal/refund', 'StorefrontController::showRefund');
$router->get('/legal/community-guidelines', 'StorefrontController::showCommunityGuidelines');
