<?php

$router->mount("/admin", function () use ($router) {
    $router->get("/", function () {
        ViewController::render('admin/home');
    });

    $router->get("/guidetypes", function () {
        $guidetypes = GuideType::getAll();
        ViewController::render('admin/guidetypes/index', ['guidetypes' => $guidetypes]);
    });

    $router->get("/guidetypes/new", function () {
        ViewController::render('admin/guidetypes/new');
    });

    $router->get("/guidetypes/{id}/edit/", function ($id) {
        $guidetype = GuideType::getById($id);
        ViewController::render('admin/guidetypes/edit', ['guidetype' => $guidetype]);
    });

    $router->get("/newscategories", function () {
        $newscategories = GameNewsCategory::getAll();
        ViewController::render('admin/newscategories/index', ['newscategories' => $newscategories]);
    });

    $router->get("/gamegenres", function () {
        $gamegenres = GameGenre::getAll();
        ViewController::render('admin/gamegenres/index', ['gamegenres' => $gamegenres]);
    });

    $router->get("/gamefeatures", function () {
        $gamefeatures = GameFeature::getAll();
        ViewController::render('admin/gamefeatures/index', ['gamefeatures' => $gamefeatures]);
    });

    $router->get("/tools", function () {
        ViewController::render('admin/tools');
    });
});

$router->set404("/admin(/.*)?", function () {
    header("HTTP/1.1 404 Not Found");
    ViewController::render('errors/404');
});
