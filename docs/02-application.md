# Creating an Application

This section will cover the process of creating an application based on this component.

## Application class

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
    protected function defineEngine(): EngineInterface|null
    {
        return new ConsoleEngine;
    }
}
```

At least two methods will need to be implemented:

1. A method that **starts** the application `engineStartAfter`. 
2. A method that defines the default Engine `defineEngine`.

The `engineStartAfter` method is called after the application has been initialized.
At this point, all dependencies and components are available, 
including `SystemEnvironment`, `Engine`, and the `Service Manager`. 

In example of a console application that will map console commands to service calls.

## What Do Applications Do?

All applications share the common characteristic that they generally invoke services in some way. 
For example, in a console application, services are invoked through console commands. 
A web server uses a dispatcher to invoke services. 
You can also create an application that invokes services based on an event queue.

Essentially, an application is a strategy that determines how service methods will be called 
and how their parameters and results will be handled.