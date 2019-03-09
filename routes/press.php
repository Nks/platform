<?php

declare(strict_types=1);

use Orchid\Press\Http\Controllers\MenuController;
use Orchid\Press\Http\Screens\EntityEditScreen;
use Orchid\Press\Http\Screens\EntityListScreen;

/*
|--------------------------------------------------------------------------
| Press Web Routes
|--------------------------------------------------------------------------
|
| Base route
|
*/

$this->router->screen('entities/{type}/{post?}/edit', EntityEditScreen::class)->name('entities.type.edit');
$this->router->screen('entities/{type}/create', EntityEditScreen::class)->name('entities.type.create');
$this->router->screen('entities/{type}/{page?}/page', EntityEditScreen::class)->name('entities.type.page');
$this->router->screen('entities/{type}', EntityListScreen::class)->name('entities.type');

$this->router->resource('menu', MenuController::class, [
    'only'  => [
        'index', 'show', 'update', 'create', 'destroy',
    ],
    'names' => [
        'index'   => 'systems.menu.index',
        'show'    => 'systems.menu.show',
        'update'  => 'systems.menu.update',
        'create'  => 'systems.menu.create',
        'destroy' => 'systems.menu.destroy',
    ],
]);
