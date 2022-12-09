<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('');
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);
//$request = \Config\Services::request();
// var_dump($request);
/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->post('register', 'Register::index');
$routes->post('login', 'Login::index');

/* Password Recovery */
$routes->post(
    'password-recovery',
    'PasswordRecovery::password_recovery'
);
$routes->put(
    'password-reset',
    'PasswordRecovery::password_rest',
    ['filter' => 'recovery']
);
/* Code Listing */
$routes->get('codes', 'Code::index', ['filter' => 'auth']);

/* Photo Content Routes*/
$routes->get('photos', 'Photo::index', [
    'filter' => 'auth',
]);
$routes->post('photos/add', 'Photo::create', [
    'filter' => 'auth',
]);
$routes->delete('photos/delete', 'Photo::delete', [
    'filter' => 'auth',
]);

/* Video Content Route */
$routes->get('videos', 'Video::index', [
    'filter' => 'auth',
]);
$routes->post('videos/add', 'Video::create', [
    'filter' => 'auth',
]);
$routes->delete('videos/delete', 'Video::delete', [
    'filter' => 'auth',
]);

/* Availability Route */
$routes->get('availability', 'Availability::index', [
    'filter' => 'auth',
]);
$routes->put(
    'availability/update',
    'Availability::update',
    ['filter' => 'auth']
);

/* Rate Settings Route */
$routes->get('baseratesettings', 'Rate::index', [
    'filter' => 'auth',
]);
$routes->put('baseratesettings/update', 'Rate::update', [
    'filter' => 'auth',
]);
$routes->post('baseratesettings/add', 'Rate::create', [
    'filter' => 'auth',
]);
$routes->delete('baseratesettings/delete', 'Rate::delete', [
    'filter' => 'auth',
]);

/* Rate Calendar Route */
$routes->get('baseratecalendar', 'RateCalendar::index', [
    'filter' => 'auth',
]);
$routes->put(
    'baseratecalendar/update',
    'RateCalendar::update',
    ['filter' => 'auth']
);

/* Filter Route */
$routes->post('filters/map', 'Filter::map', [
    'filter' => 'auth',
]);
$routes->delete('filters/delete', 'Filter::delete', [
    'filter' => 'auth',
]);

/* Promo Route */
$routes->get('promos', 'Promo::index', [
    'filter' => 'auth',
]);
$routes->post('promos/add', 'Promo::create', [
    'filter' => 'auth',
]);
$routes->put('promos/update', 'Promo::update', [
    'filter' => 'auth',
]);
$routes->delete('promos/delete', 'Promo::delete', [
    'filter' => 'auth',
]);

/* Service Route */
$routes->get('services', 'Service::index', [
    'filter' => 'auth',
]);
$routes->post('services/add', 'Service::create', [
    'filter' => 'auth',
]);
$routes->put('services/update', 'Service::update', [
    'filter' => 'auth',
]);
$routes->delete('services/delete', 'Service::delete', [
    'filter' => 'auth',
]);

/* Service price calendar Route*/
$routes->get(
    'servicecalendar',
    'ServicePriceCalendar::index',
    [
        'filter' => 'auth',
    ]
);
$routes->put(
    'servicecalendar/update',
    'ServicePriceCalendar::update',
    [
        'filter' => 'auth',
    ]
);

/* Booking Route */
$routes->get('bookings', 'Booking::index', [
    'filter' => 'auth',
]);

/* Host Route */
$routes->get('hosts', 'Host::index', ['filter' => 'auth']);
// main data
$routes->get('host/main', 'Host::main', [
    'filter' => 'auth',
]);
$routes->put('host/main/update', 'Host::main_update', [
    'filter' => 'auth',
]);
// financial data
$routes->get('host/financial', 'Host::financial', [
    'filter' => 'auth',
]);
$routes->put(
    'host/financial/update',
    'Host::financial_update',
    [
        'filter' => 'auth',
    ]
);
// lang data
$routes->get('host/lang', 'Host::lang', [
    'filter' => 'auth',
]);
$routes->put('host/lang/update', 'Host::lang_update', [
    'filter' => 'auth',
]);
// mapped property types
$routes->get(
    'host/propertytypes',
    'Host::mapped_property_types',
    ['filter' => 'auth']
);
// mapped types
$routes->get('host/types', 'Host::mapped_types', [
    'filter' => 'auth',
]);
$routes->put(
    'host/types/update',
    'Host::mapped_types_update',
    [
        'filter' => 'auth',
    ]
);

/* Stripe Route */
$routes->get('stripe', 'Stripe::index', [
    'filter' => 'auth',
]);
$routes->put('stripe/update', 'Stripe::update', [
    'filter' => 'auth',
]);

/* Property Calendar Route */
$routes->get(
    'propertycalendar',
    'PropertyCalendar::index',
    ['filter' => 'auth']
);
$routes->put(
    'propertycalendar/update',
    'PropertyCalendar::update',
    ['filter' => 'auth']
);

/* Focus on Property type */
$routes->get('propertytype', 'PropertyType::index', [
    'filter' => 'auth',
]);

/* Guest Search */
$routes->get('guests', 'Guest::index', [
    'filter' => 'auth',
]);

/* Filters Mapping */
$routes->get('filtersmapping', 'FiltersMapping::index', [
    'filter' => 'auth',
]);
$routes->put(
    'filtersmapping/update',
    'FiltersMapping::update',
    ['filter' => 'auth']
);
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (
    is_file(
        APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'
    )
) {
    require APPPATH .
        'Config/' .
        ENVIRONMENT .
        '/Routes.php';
}
