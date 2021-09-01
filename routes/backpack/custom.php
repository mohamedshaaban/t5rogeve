<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::get('api/article', 'App\Http\Controllers\Api\ArticleController@index');
Route::get('api/article-search', 'App\Http\Controllers\Api\ArticleController@search');

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    // -----
    // CRUDs
    // -----
    Route::crud('monster', 'MonsterCrudController');
    Route::crud('fluent-monster', 'FluentMonsterCrudController');
    Route::crud('icon', 'IconCrudController');
    Route::crud('product', 'ProductCrudController');
    Route::crud('dummy', 'DummyCrudController');
    Route::crud('faculty', 'FacultyCrudController');
    Route::crud('booking', 'BookingCrudController');
    Route::crud('faq', 'FaqCrudController');
    Route::crud('whoweare', 'WhoweareCrudController');
    Route::crud('wayuse', 'WayuseCrudController');
    Route::crud('contact', 'ContactCrudController');
    Route::crud('canceleventsub', 'CancelEventSubCrudController');
    Route::crud('termsconditions', 'TermsConditionsCrudController');
    Route::crud('siteaddress', 'SiteAddressCrudController');
    Route::crud('invoices', 'InvoicesCrudController');
    Route::crud('SponsorPlatinums', 'SponsorPlatinumsCrudController');
    Route::crud('events', 'EventsCrudController');
    Route::crud('customers', 'CustomersCrudController');
    Route::post('fetch/faculty', 'FacultyCrudController@fetch');
    Route::post('fetch/ceremony', 'EventsCrudController@fetch');
    Route::post('fetch/bookinguser', 'EventsCrudController@fetchuser');
    Route::get('fetch/eventdetails/{id}', 'EventsCrudController@fetchEventDetails');
    Route::get('fetch/studentdetails/{id}', 'CustomersCrudController@fetchStudentDetails');
    Route::get('fetch/bookingfilteruser', 'CustomersCrudController@studentOptions');
    Route::get('fetch/eventfilteruser', 'EventsCrudController@eventOptions');

    // ------------------
    // AJAX Chart Widgets
    // ------------------
    Route::get('charts/users', 'Charts\LatestUsersChartController@response');
    Route::get('charts/new-entries', 'Charts\NewEntriesChartController@response');

    // ---------------------------
    // Backpack DEMO Custom Routes
    // Prevent people from doing nasty stuff in the online demo
    // ---------------------------
    Route::get('/switch_lang/{locale}', function ($locale = '') {
        session(['locale' => $locale]);
        app()->setLocale($locale);
        App::setLocale($locale);
        return redirect()->back();
    })->name('switch_lang');
    if (app('env') == 'production') {
        // disable delete and bulk delete for all CRUDs
        $cruds = ['article', 'category', 'tag', 'monster', 'icon', 'product', 'page', 'menu-item', 'user', 'role', 'permission'];
        foreach ($cruds as $name) {
            Route::delete($name.'/{id}', function () {
                return false;
            });
            Route::post($name.'/bulk-delete', function () {
                return false;
            });
        }
    }
}); // this should be the absolute last line of this file
