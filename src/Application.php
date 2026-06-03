<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App;

use App\Event\DatabaseEncryptor;
use App\Identifier\Resolver\SessionResolver;
use App\Middleware\HostHeaderMiddleware;
use App\Service\Encryption\GpgService;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Authorization\AuthorizationService;
use Authorization\AuthorizationServiceInterface;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Middleware\AuthorizationMiddleware;
use Authorization\Policy\OrmResolver;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Datasource\FactoryLocator;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Event\EventManagerInterface;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\ORM\Locator\TableLocator;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 *
 * @extends \Cake\Http\BaseApplication<\App\Application>
 */
class Application extends BaseApplication
    implements AuthenticationServiceProviderInterface, AuthorizationServiceProviderInterface
{
    /**
     * Load all the application configuration and bootstrap logic.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        $this->addPlugin('Authorization');

        // By default, does not allow fallback classes.
        FactoryLocator::add('Table', (new TableLocator())->allowFallbackClass(false));
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error'), $this))

            // Validate Host header to prevent Host Header Injection attacks.
            // In production, ensures App.fullBaseUrl is configured and validates
            // the incoming Host header against it.
            ->add(new HostHeaderMiddleware())

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(
                new AssetMiddleware([
                    'cacheTime' => Configure::read('Asset.cacheTime'),
                ])
            )

            // Add routing middleware.
            // If you have a large number of routes connected, turning on routes
            // caching in production could improve performance.
            // See https://github.com/CakeDC/cakephp-cached-routing
            ->add(new RoutingMiddleware($this))

            // Parse various types of encoded request bodies so that they are
            // available as array through $request->getData()
            // https://book.cakephp.org/5/en/controllers/middleware.html#body-parser-middleware
            ->add(new BodyParserMiddleware())
            ->add(new AuthenticationMiddleware($this))
            ->add(new AuthorizationMiddleware($this));

        // Cross Site Request Forgery (CSRF) Protection Middleware
        // https://book.cakephp.org/5/en/security/csrf.html#cross-site-request-forgery-csrf-middleware
//            ->add(new CsrfProtectionMiddleware([
//                'httponly' => true,
//            ])); todo 0.3 re-enable! Disabled temporarily to not further derail tutorial progress, with upcoming deadline
        ;
        return $middlewareQueue;
    }

    /**
     * Register application container services.
     *
     * @param \Cake\Core\ContainerInterface $container The Container to update.
     * @return void
     * @link https://book.cakephp.org/5/en/development/dependency-injection.html#dependency-injection
     */
    public function services(ContainerInterface $container): void
    {
        //todo 0.4 figure out why configuration wiring isn't working as expected; addArgument should not be required?
        $container->add(GpgService::class)
            ->addArgument(Configure::read('Gpg.default.keyFingerprint'))
            ->addArgument(Configure::read('Gpg.default.keyPassword'))
            ->addArgument(Configure::read('Gpg.default.dir'))
            ->addArgument(Configure::read('Gpg.errorMode'));
        // Allow your Tables to be dependency injected ... sadly it seems this means Tables can be injected into stuff, not that we can inject into Tables... :(
        //$container->delegate(new \Cake\ORM\Locator\TableContainer());
    }

    /**
     * Register custom event listeners here
     *
     * @param \Cake\Event\EventManagerInterface $eventManager
     * @return \Cake\Event\EventManagerInterface
     * @link https://book.cakephp.org/5/en/core-libraries/events.html#registering-listeners
     */
    public function events(EventManagerInterface $eventManager): EventManagerInterface
    {
         $eventManager->on(new DatabaseEncryptor($this->getContainer()->get(GpgService::class)));

        return $eventManager;
    }

    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $service = new AuthenticationService();

        // Define where users should be redirected to when they are not authenticated
        $service->setConfig([
            'unauthenticatedRedirect' => null,
        ]);

        // Load the authenticators. Session should be first.
        //todo 0.4 switch to JWT?
        $service->loadAuthenticator('Authentication.Token', [
            'header' => 'Authorization',
            'identifier' => [
                'className' => 'Authentication.Token',
                //'hashAlgorithm' => 'sha256' is an option, but meh...
                'resolver' => [
                    'className' => SessionResolver::class,
                ],
            ],
        ]);
        $service->loadAuthenticator('Authentication.Form', [
            'loginUrl' => [
                'prefix' => false,
                'plugin' => null,
                'controller' => 'Users',
                'action' => 'login',
            ],
            'identifier' => [
                'className' => 'Authentication.Password',
            ],
        ]);

        return $service;
    }

    public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
    {
        //todo 0.4 is this some tutorial leftovers? I know I'm using a custom SessionResolver...
        $resolver = new OrmResolver();

        return new AuthorizationService($resolver);
    }
}
