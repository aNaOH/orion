<?php

$router->mount("/admin", function () use ($router) {
    $router->get("/", function () {
        include "views/admin/home.php";
    });

    $router->get("/guidetypes", function () {
        $guidetypes = GuideType::getAll();
        $GLOBALS["guidetypes"] = $guidetypes;
        include "views/admin/guidetypes/index.php";
    });

    $router->get("/guidetypes/new", function () {
        include "views/admin/guidetypes/new.php";
    });

    $router->get("/guidetypes/{id}/edit/", function ($id) {
        $guidetype = GuideType::getById($id);
        $GLOBALS["guidetype"] = $guidetype;
        include "views/admin/guidetypes/edit.php";
    });

    $router->get("/newscategories", function () {
        $newscategories = GameNewsCategory::getAll();
        $GLOBALS["newscategories"] = $newscategories;
        include "views/admin/newscategories/index.php";
    });

    $router->get("/gamegenres", function () {
        $gamegenres = GameGenre::getAll();
        $GLOBALS["gamegenres"] = $gamegenres;
        include "views/admin/gamegenres/index.php";
    });

    $router->get("/gamefeatures", function () {
        $gamefeatures = GameFeature::getAll();
        $GLOBALS["gamefeatures"] = $gamefeatures;
        include "views/admin/gamefeatures/index.php";
    });
});

$router->set404("/admin(/.*)?", function () {
    header("HTTP/1.1 404 Not Found");
    include "views/admin/404.php";
});
