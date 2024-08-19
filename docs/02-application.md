# Creating an Application

This section will cover the process of creating an application based on this component.

## Application and Engine

`Engine` is a set of modules that manage low-level components such as timers, workers, processes, 
and asynchronous operations. 
This allows different applications to be built on the same `Engine`.

The role of the `Application` is to initialize the runtime environment, 
load all dependencies, and ultimately start the `Engine`.

In essence, the `Application` is the strategy for launching the application, 
while the `Engine` is its low-level core. The `ServiceManager` represents the business logic, 
and the `SystemEnvironment` is the lifeblood that ties everything together.

Thus, you can have multiple "applications" for the same project. 
Essentially, you can have multiple running processes-entry points-each of which can operate slightly differently. 
This approach can be extremely useful: having a single codebase with different ways to use it.

## Application class

There are two ways to structure an application:

* When the application complements the `Engine`.
* When the application defines the `Engine`.

In the first case, the `Application` is clearly independent of a specific `Engine`, 
allowing for different `Engines` to be used. 

In the second case, the `Application` **depends on** a specific `Engine`.

Below is an example of a Console application.

Creating the main application class might look like this:

```php
<?php
declare(strict_types=1);

namespace IfCastle\Console;

use IfCastle\Application\ApplicationAbstract;
use IfCastle\Application\EngineInterface;
use IfCastle\ServiceManager\DescriptorRepositoryInterface;

class ConsoleApplication            extends ApplicationAbstract
{
    #[\Override]
    protected function engineStartAfter(): void
    {
        (new SymfonyApplication(
            $this->systemEnvironment,
            $this->systemEnvironment->resolveDependency(DescriptorRepositoryInterface::class)
        ))->run();
    }
    
    #[\Override]
    protected function defineEngineRole(): EngineRolesEnum
    {
        return EngineRolesEnum::CONSOLE;
    }        
}
```

At least two methods will need to be implemented:

1. A method that **starts** the application `engineStartAfter`. 
2. A method that defines the default Role `defineEngineRole`.

The `engineStartAfter` method is called after the application has been initialized.
At this point, all dependencies and components are available, 
including `SystemEnvironment`, `Engine`, and the `Service Manager`. 

In example of a console application that will map console commands to service calls.

Web-server type applications are generally required to define the `Engine`. 
Why? The reason is that the operation of a web server is closely tied to the type of `Engine`, 
and currently, there is no scenario where a web server can function independently of an `Engine`. 
Therefore, in the case of a web server, you will see code like this:

```php
class WebServerApplication          extends ApplicationAbstract
{
    #[\Override]
    protected static function predefineEngine(BootloaderExecutorInterface $bootloaderExecutor): void
    {
        $bootloaderExecutor->getBootloaderContext()->getSystemEnvironmentBootBuilder()
            ->set(EngineInterface::class, new ConstructibleDependency(WebServerEngine::class));
    }
    
    #[\Override]
    protected function defineEngineRole(): EngineRolesEnum
    {
        return EngineRolesEnum::SERVER;
    }
}
```

In this case, the `predefineEngine` method is used to define the `Engine` that will be used by the application.
The Engine dependency becomes available at the moment the application is launched, 
and other components can make decisions about their operation based on this dependency.

## Application::postConfigureBootloader

The `postConfigureBootloader` method also allows configuring 
the Bootloader before the application's initialization process begins. 
At the `postConfigureBootloader` stage, the fully configured `BootloaderExecutor` is available. 
This means the application can modify its configuration at the last moment before the launch.

## What Do Applications Do?

All applications share the common characteristic that they generally invoke services in some way. 
For example, in a console application, services are invoked through console commands. 
A web server uses a dispatcher to invoke services. 
You can also create an application that invokes services based on an event queue.

Essentially, an application is a strategy that determines how service methods will be called 
and how their parameters and results will be handled.