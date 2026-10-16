<?php
/** @var Router $router */
$A = ['admin'];
$D = ['dev'];
$U = ['auth'];

// ===== KHU VỰC USER =====
$router->get('/', 'HomeController@index');
$router->post('/newsletter', 'HomeController@newsletter');
$router->get('/lang/{code}', 'HomeController@lang');

$router->get('/products', 'ProductController@index');
$router->get('/products/detail', 'ProductController@detail');
$router->get('/categories', 'ProductController@categories');

$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->post('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@forgotForm');
$router->post('/forgot-password', 'AuthController@forgot');
$router->get('/reset-password', 'AuthController@resetForm');
$router->post('/reset-password', 'AuthController@reset');
$router->get('/reset-otp', 'AuthController@otpForm');
$router->post('/reset-otp', 'AuthController@otpReset');

$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/voucher', 'CartController@applyVoucher');
$router->post('/cart/voucher-remove', 'CartController@removeVoucher');

$router->get('/checkout', 'CheckoutController@index', $U);
$router->post('/checkout', 'CheckoutController@place', $U);

$router->get('/orders', 'OrderController@index', $U);
$router->get('/orders/detail', 'OrderController@detail', $U);
$router->post('/orders/cancel', 'OrderController@cancel', $U);

$router->get('/profile', 'ProfileController@index', $U);
$router->post('/profile/update', 'ProfileController@update', $U);
$router->post('/profile/password', 'ProfileController@password', $U);

$router->get('/inbox', 'InboxController@index', $U);
$router->get('/inbox/view', 'InboxController@show', $U);
$router->post('/inbox/delete', 'InboxController@delete', $U);

$router->get('/reviews', 'ReviewController@index', $U);
$router->post('/reviews/store', 'ReviewController@store', $U);

$router->get('/contact', 'ContactController@index');
$router->post('/contact', 'ContactController@send');

$router->get('/about', 'PageController@about');
$router->get('/policies', 'PageController@policies');
$router->get('/faq', 'PageController@faq');
$router->get('/size-guide', 'PageController@sizeGuide');

// ===== KHU VỰC ADMIN =====
$router->get('/admin', 'AdminDashboardController@index', $A);
$router->get('/admin/reports', 'AdminReportController@index', $A);

$router->get('/admin/products', 'AdminProductController@index', $A);
$router->get('/admin/products/create', 'AdminProductController@form', $A);
$router->get('/admin/products/edit/{id}', 'AdminProductController@form', $A);
$router->post('/admin/products/save', 'AdminProductController@save', $A);
$router->post('/admin/products/save/{id}', 'AdminProductController@save', $A);
$router->post('/admin/products/toggle/{id}', 'AdminProductController@toggle', $A);
$router->post('/admin/products/delete/{id}', 'AdminProductController@delete', $A);
$router->post('/admin/products/{id}/variants', 'AdminProductController@addVariant', $A);
$router->post('/admin/variants/update/{id}', 'AdminProductController@updateVariant', $A);
$router->post('/admin/variants/delete/{id}', 'AdminProductController@deleteVariant', $A);
$router->post('/admin/images/delete/{id}', 'AdminProductController@deleteImage', $A);

$router->get('/admin/users', 'AdminUserController@index', $A);
$router->get('/admin/users/view/{id}', 'AdminUserController@show', $A);
$router->post('/admin/users/toggle/{id}', 'AdminUserController@toggle', $A);
$router->post('/admin/users/message/{id}', 'AdminUserController@message', $A);

$router->get('/admin/employees', 'AdminEmployeeController@index', $A);
$router->post('/admin/employees/save', 'AdminEmployeeController@save', $A);
$router->post('/admin/employees/toggle/{id}', 'AdminEmployeeController@toggle', $A);

$router->get('/admin/orders', 'AdminOrderController@index', $A);
$router->get('/admin/orders/view/{id}', 'AdminOrderController@show', $A);
$router->post('/admin/orders/status/{id}', 'AdminOrderController@status', $A);

// Các module CRUD dùng chung
$crud = [
    'categories' => 'AdminCategoryController',
    'vouchers'   => 'AdminVoucherController',
    'promotions' => 'AdminPromotionController',
    'banners'    => 'AdminBannerController',
    'reviews'    => 'AdminReviewController',
    'contacts'   => 'AdminContactController',
];
foreach ($crud as $r => $c) {
    $router->get("/admin/$r", "$c@index", $A);
    $router->get("/admin/$r/create", "$c@form", $A);
    $router->get("/admin/$r/edit/{id}", "$c@form", $A);
    $router->post("/admin/$r/save", "$c@save", $A);
    $router->post("/admin/$r/save/{id}", "$c@save", $A);
    $router->post("/admin/$r/toggle/{id}", "$c@toggle", $A);
    $router->post("/admin/$r/delete/{id}", "$c@delete", $A);
}

// ===== KHU VỰC DEVELOPER =====
$router->get('/developer', 'DeveloperController@status', $D);
$router->get('/developer/logs', 'DeveloperController@logs', $D);
