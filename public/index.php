<?php
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'on');
    ini_set('error_log', '/home/children/help-children/var/logs/php.log');
    error_reporting(E_ALL);

    if (isset($_GET['phpinfo'])) if (intval($_GET['phpinfo']) == 1) {
        phpinfo();
        exit;
    }

    use App\Kernel;
    use Symfony\Component\Debug\Debug;
    use Symfony\Component\HttpFoundation\Request;

    require dirname(__DIR__).'/config/bootstrap.php';

    if ($_SERVER['APP_DEBUG']) {
        umask(0000);
        Debug::enable();
    }

    if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
        Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
    }

    if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
        Request::setTrustedHosts([$trustedHosts]);
    }

    $kernel   = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
    $request  = Request::createFromGlobals();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);