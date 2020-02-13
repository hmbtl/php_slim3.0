<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/include/functions.php';

$app = new Slim\App([
    'settings' => [
    'displayErrorDetails' => true,
    'debug'               => true
    ]
]);

$app->add(function ($request, $response, $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));
        
        if($request->getMethod() == 'GET') {
            return $response->withRedirect((string)$uri, 301);
        }
        else {
            return $next($request->withUri($uri), $response);
        }
    }

    return $next($request, $response);
});

// check if route is found then return json response
$app->add(function ($request, $response, $next) use ($app) {
    // execute requests
    $response = $next($request, $response);

    // check status of the request
    if (404 === $response->getStatusCode()) {
    // return error
    return error($response, 400);
    }
    // return actual response
    return $response;
});

// get list of all users
$app->get('/errors', 'getErrors');

// get all user information using token
$app->get('/user','getUser')->add('authenticate');

$app->run();

?>
