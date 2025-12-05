# PHPolar Changelog

This file documents changes to the PHPolar project

## 8.2.3 (2025-12-04)

## 8.2.2 (2025-12-04)

## 8.2.2 (2025-11-24)

## 8.2.1 (2025-11-17)

### Refactor

- rename to request authorizer (#512)

## 8.2.0 (2025-11-17)

### Feat

- make class readonly and add sensitive parameter attributes (#510)

## 8.1.5 (2025-11-12)

### Fix

- use authenticated request processor (#504)

## 8.1.4 (2025-10-27)

## 8.1.3 (2025-10-13)

## 8.1.2 (2025-10-06)

## 8.1.1 (2025-09-28)

## 8.1.0 (2025-07-05)

### Feat

- deprecate response builder (#451)

## 8.0.2 (2025-06-29)

### Fix

- reduce distributed package size

## 8.0.1 (2025-06-29)

### Fix

- move interface to http-request-processor (#448)

## 8.0.0 (2025-06-29)

### Feat

- use http request processor (#447)

## 7.3.0 (2025-06-29)

### Feat

- add support for returning http statuses (#446)

## 7.2.1 (2025-06-28)

### Fix

- remove unused exception class (#444)

## 7.2.0 (2025-06-28)

### Feat

- move representations and serialzers into packages (#443)

## 7.1.0 (2025-06-22)

### Feat

- use phpolar/model  2.1.0

## 7.0.0 (2025-06-22)

### Feat

- attach content types to responses (#440)
- add support for http request negotiation (#435)

### Fix

- merge model and path variables instead of intersecting (#438)
- **RequestProcessingHandler**: remove model resolver named arguments (#437)

## 6.1.2 (2025-05-19)

## 6.1.1 (2025-05-05)

## 6.1.0 (2025-04-23)

### Feat

- add delete and put request methods (#411)

## 6.0.2 (2025-04-21)

### Fix

- **ContainerLoader**: improve dependency loading type safety (#403)

### Refactor

- **ContainerLoader**: shorten variable name (#405)

## 6.0.1 (2025-04-16)

## 6.0.0 (2025-03-19)

### BREAKING CHANGE

- PHP >= 8.3 required

## 5.2.0 (2025-02-09)

### Feat

- **ContainerLoader**: add alpine linux support (#373)

## 5.1.0 (2025-01-20)

### Feat

- **composer**: upgrade dependencies

## 5.0.4 (2025-01-19)

### Fix

- **pure-php**: rollback to 2.0 (#371)

## 5.0.3 (2025-01-19)

### Fix

- **readme**: update badges (#369)

## 5.0.2 (2024-12-01)

### Fix

- **composer**: upgrade phpolar-core to 4.0.1

## 5.0.1 (2024-12-01)

### Fix

- **composer**: upgrade dependencies

## 5.0.0 (2023-09-04)

### Feat

- upgrade laminas-httphandlerrunner to 2.9 (#299)
- use php-contrib interfaces (#292)
- use routable factory interface (#285)
- add support for route target factories (#284)
- **ContainerLoader**: reorder method parameters (#283)

### Fix

- Rebase fix (#295)
- remove unused files (#294)
- **RouteMap**: correct parameterized path matching (#290)
- **RouteMap**: create instances on demand (#286)

### Refactor

- use pure-php v2 (#291)

## 4.0.0 (2023-09-03)

### BREAKING CHANGE

- Declare properties that need to be injected for routables and use an implementation of https://packagist.org/providers/phpolar/property-injector-contract-implementation.  A PSR-11 container will no longer be provided to routables.
- `RouteRegistry` has been renamed to `RouteMap`.
- The `ErrorHandler` class has been removed. Templates with response code names will no longer be used for error handling. Most servers support error pages based on response codes.
- `ClosureContainerFactory` and `ContainerFactoryInterface` were removed.  Using `ContainerLoader` is required to include framework and custom dependencies in the DI container.
- All classes in the `Routing` namespace have been moved to the `Http` namespace.  `ModelParamResolver` has been renamed to `ModelResolver`.  The `ModelResolver` now takes the parsed request body as the second constructor argument instead of the server request.  The `PrimaryHandler` has been renamed to `MiddlewareQueueRequestHandler`.  Issue #174.
- Add the `RouteRegistry` in a dependency injection configuration file instead of calling `App::useRoutes`.
- All classes in the `Model` namespace have been moved to the `phpolar/model` project.  An implementation of `ModelResolverInterface` MUST added to the dependency injection configuration.  Closes #174.

### Feat

- use request method enums instead of string (#272)
- use property injector instead of service locator pattern (#271)
- **App**: add support for queueing middleware (#248)
- add support for route authorization (#243)
- move interfaces out of project (#238)
- add support for authenticated routables (#218)

### Fix

- **di**: correct dependency ids (#250)
- remove bin folder (#237)
- **RouteRegistry**: reindex projected array (#232)
- retrieve the user object directly (#228)
- **AbstractRestrictedAccessRequestProcessor**: use session user object (#227)
- add support for configurable unauthorized handlers (#226)
- **RoutingHandler**: set the user property (#225)
- prune dist (#201)

### Refactor

- **RoutingHandler**: reduce dependency count (#249)
- rename authenticate attribute to authorize (#247)
- **RouteRegistry**: rename to route map (#245)
- remove error handler class (#241)
- **RoutableInterface**: move interface out of http namespace (#220)
- use newly renamed core library (#209)
- **RouteRegistry**: reduce complexity (#206)
- remove unused test helpers (#205)
- remove unused classes (#199)
- simplify project architecture (#180)

## 2.0.2 (2023-09-03)

### BREAKING CHANGE

- Please see https://api.phpolar.org and https://docs.phpolar.org.
- https://github.com/phpolar/phpolar-core/issues/12
- Issue #58

### Feat

- project rewrite
- add support for model attributes
- pass psr 11 container instead of config array
- add support for configuring properties as primary keys
- add support for parameterized routes
- add routing handler and middleware
- select validation html attribute string based on model state
- update method signatures
- add use authentication method
- **DefaultRoutingHandler**: add support for custom 404 error handling
- add launch script
- **AbstractModel**: hydrate model when it is created
- **FieldErrorMessageTrait**: use method instead of property for posted state
- make error handling generic
- automatically configure dependencies
- change name of abstract request handler
- allow using basename for template files
- **RouteRegistry**: add routing based on request methods
- **FieldErrorMessageTrait**: add haserror method
- **WebServer**: add useroutes method
- **FieldErrorMessageTrait**: add support for appending to the field error message
- add phpolar-storage, use phpunit 10 😀
- rewrite project
- add validation attributes

### Fix

- remove .phpdoc from dist (#173)
- use csrf-protection 3
- **AbstractModel**: add null to constructor parameter type hint
- **Model**: use correct attribute target
- **WebServer**: make web server singleton
- bump alpha version
- bump alpha version
- **AbstractModel**: all iteration over all public properties
- return declared types only
- bump alpha version
- **AbstractModel**: add primary key trait
- bump alpha version
- bump alpha version
- add support for parameterized routes for post requests
- bump alpha version
- use 404 error handler
- remove unused dependency
- bump version
- do not set dynamic properties
- get di configuration from the installed path
- **MiddlewareProcessingQueue**: handle unauthorized requests from csrf middleware
- **AbstractModel**: prevent type errors
- **FieldErrorMessageTrait**: only validate posted forms
- respond to post requests
- use container implementation/config instead of psr-11 container
- **AbstractRouteDelegate**: provide the di container
- **ContainerFactory**: provide a container factory implementation
- require container configuration when creating app
- do not initialize the checked property
- exclude unnecessary files from dist
- exclude unnecessary files from dist
- upgrade library, use remote repo instead of local

### Refactor

- **RoutingHandler**: use type doc annotation instead of casting with strval
- move validators to lib
- rename web server to app
- move formats to core
- restructure namespaces
- move classes to core
- **FormControlTypeDetectionTraitTest**: remove unused import
- **PrimaryHandler**: handle null result explicitly
- simplify config loading
- load routes in container, update container manager
- add primary handler
- upgrade csrf library, simplify web server implementation
- use formats from enum
- simplify implementation
- move format strings to enum
- **WebServer**: change method name
- rename abstract route delegate to abstract content delegate
- do not send response in middleware queue
- use newly named library
- use newly renamed template library
- create model and webserver namespaces
- remove classes

## 1.6.5 (2023-09-03)

### Feat

- **Entry.php**: ensure order of fields is maintained
- add support for enumerations
- add support for php 8 attributes
- **App.php**: use in-memory configuration by default
- **app.php**: use csv-file-storage by default
- **app.php**: use csv-file-storage by default
- **csv-file-storage.php**: move csv-file-storage
- **htmlencoder.php**: move to core/rendering
- **automatic-date-field.php**: remove dependency to date field
- **polar**: use typed properties
- **polar**: use arrow functions

### Fix

- change package name
- fix tests
- fix tests
- **composer.json**: exclude test classes from installation
- remove duplicate property setting
- use value of property when checking max length
- properly configure fields when using native attributes
- php 8 deprecations
- file permission errors windows (#37)
- **Core/Parsers/ConstructorArgsOne**: allow unquoted constructor args in annotations

### Refactor

- remove unused imports, use phpstan in ci
- fix phpmd errors, add pre-commit-config
- use readonly properties
- use constructor property promotion
- use union types
- use match expressions
- remove stringable polyfill
- **Form.php**: move to root folder of Api namespace
- **Comparable.php**: move to core
- **HtmlEncoder.php**: reduce cyclomatic complexity (#29)
- **TypeValidation.php**: reduce cyclomatic complexity
- **field.php**: reduce cyclomatic complexity (#27)
