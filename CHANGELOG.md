## 3.0.0rc2 (2023-07-18)

### Fix

- add support for configurable unauthorized handlers (#226)
- **RoutingHandler**: set the user property (#225)

## 3.0.0rc1 (2023-07-18)

### Refactor

- **RoutableInterface**: move interface out of http namespace (#220)

## 3.0.0rc0 (2023-07-17)

### Feat

- add support for authenticated routables (#218)

## 3.0.0b0 (2023-07-03)

### Fix

- prune dist (#201)

### Refactor

- use newly renamed core library (#209)
- **RouteRegistry**: reduce complexity (#206)
- remove unused test helpers (#205)

## 3.0.0a1 (2023-06-23)

### Refactor

- remove unused classes (#199)

## 3.0.0a0 (2023-06-23)

### Refactor

- simplify project architecture (#180)

## 2.0.2 (2023-06-08)

### Fix

- remove .phpdoc from dist (#173)

## 2.0.1 (2023-05-22)

### Fix

- use csrf-protection 3

## 2.0.0 (2023-05-01)

### BREAKING CHANGE

- Please see https://api.phpolar.org and https://docs.phpolar.org.

### Feat

- project rewrite

## 2.0.0b2 (2023-04-29)

### Fix

- **AbstractModel**: add null to constructor parameter type hint

## 2.0.0b1 (2023-04-29)

### Fix

- **Model**: use correct attribute target

## 2.0.0b0 (2023-04-04)

### Feat

- add support for model attributes

### Refactor

- **RoutingHandler**: use type doc annotation instead of casting with strval

## 2.0.0a1 (2023-04-01)

## 2.0.0a0.dev0 (2023-04-01)

### BREAKING CHANGE

- https://github.com/phpolar/phpolar-core/issues/12
- Issue #58

### Feat

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

## 1.6.5 (2023-01-16)

### Fix

- change package name

## 1.6.4 (2022-12-04)

### Fix

- fix tests

## 1.6.3 (2022-12-04)

### Fix

- fix tests

## 1.6.2 (2022-12-04)

### Fix

- **composer.json**: exclude test classes from installation

## 1.6.1 (2022-10-22)

### Fix

- remove duplicate property setting

## 1.6.0 (2022-10-22)

### Feat

- **Entry.php**: ensure order of fields is maintained

## 1.5.2 (2022-10-21)

### Fix

- use value of property when checking max length

## 1.5.1 (2022-10-20)

### Fix

- properly configure fields when using native attributes

### Refactor

- remove unused imports, use phpstan in ci
- fix phpmd errors, add pre-commit-config

## 1.5.0 (2022-08-08)

### Feat

- add support for enumerations

### Refactor

- use readonly properties

### Fix

- php 8 deprecations

## php8-0 (2022-08-06)

## 1.4.0 (2022-08-06)

### Feat

- add support for php 8 attributes

## 1.3.1 (2022-08-05)

### Fix

- file permission errors windows (#37)

## 1.3.0 (2022-08-01)

### Feat

- **App.php**: use in-memory configuration by default

### Refactor

- **Form.php**: move to root folder of Api namespace
- **Comparable.php**: move to core
- **Comparable.php**: move to core
- **HtmlEncoder.php**: reduce cyclomatic complexity (#29)

## 1.2.0 (2022-07-31)

### Refactor

- **HtmlEncoder.php**: reduce cyclomatic complexity
- **TypeValidation.php**: reduce cyclomatic complexity
- **TypeValidation.php**: reduce cyclomatic complexity
- **Field.php**: reduce cyclomatic complexity (#27)

### Feat

- **App.php**: use csv-file-storage by default
- **CsvFileStorage.php**: move csv-file-storage
- **HtmlEncoder.php**: move to core/rendering
- **AutomaticDateField.php**: remove dependency to date field

## 1.1.0 (2022-07-17)

## php7-4 (2022-07-17)

### Feat

- **polar**: use arrow functions
- **polar**: use arrow functions
- **polar**: use typed properties

## 1.0.1 (2022-07-17)

## php7-3 (2022-07-17)

### Fix

- **Core/Parsers/ConstructorArgsOne**: allow unquoted constructor args in annotations

## 1.0.0 (2022-07-09)
