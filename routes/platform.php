<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;


use App\Orchid\Screens\Category\CategoryCreateScreen;
use App\Orchid\Screens\Category\CategoryEditScreen;
use App\Orchid\Screens\Category\CategoryListScreen;

use App\Orchid\Screens\Shop\ShopCreateScreen;
use App\Orchid\Screens\Shop\ShopEditScreen;
use App\Orchid\Screens\Shop\ShopListScreen;

use App\Orchid\Screens\Banner\BannerCreateScreen;
use App\Orchid\Screens\Banner\BannerEditScreen;
use App\Orchid\Screens\Banner\BannerListScreen;

use App\Orchid\Screens\News\NewsCreateScreen;
use App\Orchid\Screens\News\NewsEditScreen;
use App\Orchid\Screens\News\NewsListScreen;

use App\Orchid\Screens\Vedomstvo\VedomstvoCreateScreen;
use App\Orchid\Screens\Vedomstvo\VedomstvoEditScreen;
use App\Orchid\Screens\Vedomstvo\VedomstvoListScreen;

use App\Orchid\Screens\Product\ProductCreateScreen;
use App\Orchid\Screens\Product\ProductEditScreen;
use App\Orchid\Screens\Product\ProductListScreen;

use App\Orchid\Screens\Product\ProductGaleryCreateScreen;
use App\Orchid\Screens\Product\ProductGaleryEditScreen;

use App\Orchid\Screens\Product\ProductPriceCreateScreen;
use App\Orchid\Screens\Product\ProductPriceEditScreen;


use App\Orchid\Screens\Options\OptionsList;
use App\Orchid\Screens\Options\EditOptions;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Категории
Route::screen('/categories', CategoryListScreen::class)
    ->name('platform.category')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push(__('Категории товаров'), route('platform.category')));

Route::screen('/categories/{id}/edit', CategoryEditScreen::class)
    ->name('platform.category_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.category')
    ->push(__('Редактирование категории'), route('platform.category_edit', $id)));

Route::screen('/categories/create', CategoryCreateScreen::class)
    ->name('platform.category_create')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.category')
    ->push(__('Добавление категории'), route('platform.category_create')));

// Ведомства
Route::screen('/vedomstva', VedomstvoListScreen::class)
    ->name('platform.vedomstva')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push(__('Ведомства'), route('platform.vedomstva')));

Route::screen('/vedomstva/{id}/edit', VedomstvoEditScreen::class)
    ->name('platform.vedomstva_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.vedomstva')
    ->push(__('Редактирование ведомства'), route('platform.vedomstva_edit', $id)));

Route::screen('/vedomstva/create', VedomstvoCreateScreen::class)
    ->name('platform.vedomstva_create')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.vedomstva')
    ->push(__('Добавление ведомства'), route('platform.vedomstva_create')));

// Магазины
Route::screen('/shops', ShopListScreen::class)
    ->name('platform.shops')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push(__('Магазины'), route('platform.shops')));

Route::screen('/shops/{id}/edit', ShopEditScreen::class)
    ->name('platform.shops_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.shops')
    ->push(__('Редактирование магазина'), route('platform.shops_edit', $id)));

Route::screen('/shops/create', ShopCreateScreen::class)
    ->name('platform.shops_create')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.shops')
    ->push(__('Добавление магазина'), route('platform.shops_create')));

// Баннеры
Route::screen('/banner', BannerListScreen::class)
    ->name('platform.banner')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push(__('Баннеры'), route('platform.banner')));

Route::screen('/banner/{id}/edit', BannerEditScreen::class)
    ->name('platform.banner_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.banner')
    ->push(__('Редактирование Баннера'), route('platform.banner_edit', $id)));

Route::screen('/banner/create', BannerCreateScreen::class)
    ->name('platform.banner_create')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.banner')
    ->push(__('Добавление Баннера'), route('platform.banner_create')));

// Новости
Route::screen('/news', NewsListScreen::class)
    ->name('platform.news')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push(__('Новости'), route('platform.news')));

Route::screen('/news/{id}/edit', NewsEditScreen::class)
    ->name('platform.news_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.news')
    ->push(__('Редактирование Новости'), route('platform.news_edit', $id)));

Route::screen('/news/create', NewsCreateScreen::class)
    ->name('platform.news_create')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.news')
    ->push(__('Добавление Новости'), route('platform.news_create')));


// Товары
Route::screen('/products', ProductListScreen::class)
    ->name('platform.product')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push(__('Товары'), route('platform.product')));

Route::screen('/products/{id}/edit', ProductEditScreen::class)
    ->name('platform.product_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.product')
    ->push(__('Редактирование товара'), route('platform.product_edit', $id)));

Route::screen('/products/create', ProductCreateScreen::class)
    ->name('platform.product_create')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.product')
    ->push(__('Добавление товара'), route('platform.product_create')));

Route::screen('/products/galery/{id}/edit', ProductGaleryEditScreen::class)
    ->name('platform.product_galery_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.product')
    ->push(__('Редактирование изображения галереи'), route('platform.product_galery_edit', $id)));

Route::screen('/products/{id}/galery/create', ProductGaleryCreateScreen::class)
    ->name('platform.product_galery_create')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.product')
    ->push(__('Создание изображения галереи'), route('platform.product_galery_create', $id)));

Route::screen('/products/price/{id}/edit', ProductPriceEditScreen::class)
    ->name('platform.product_price_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.product')
    ->push(__('Редактирование ценового предложения'), route('platform.product_price_edit', $id)));

Route::screen('/products/{id}/price/create', ProductPriceCreateScreen::class)
    ->name('platform.product_price_create')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.product')
    ->push(__('Редактирование ценового предложения'), route('platform.product_price_create', $id)));

// Опции
Route::screen('/options', OptionsList::class)
    ->name('platform.options')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Опции'), route("platform.options")));

Route::screen('/options/{id}/edit', EditOptions::class)
    ->name('platform.options_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.options')
    ->push(__('Редактирование опции'), route('platform.options_edit', $id)));





// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

//Route::screen('idea', Idea::class, 'platform.screens.idea');
