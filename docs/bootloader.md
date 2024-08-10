# Bootloader

## Bootloader Process

The `Bootloader` component handles the application's loading 
and initialization process. 

The process is carried out in several stages:

```puml
@startuml
title Bootloader Process

start
:Bootloader Build;
:Bootloader Execution;
:Engine Launch;
stop

@enduml
```

Bootloader construction is performed by the `BootloaderManager`, which executes the following stages:

* Loading the application configuration
* Loading all Bootloader classes that form the `BootloaderExecutor`

```puml
@startuml
title Bootloader Process

start
group Bootloader Build
  :Loading application configuration;
  :Loading Bootloader classes;
end group
:Bootloader Execution;
:Engine Launch;
stop

@enduml

```

The `BootloaderExecutor` executes handlers in a specified sequence of three stages:

* BeforeAction - Initializes the environment. 
Executed before the `Application` and `SystemEnvironment` classes are created.
* BuildApplication - Creates the application and the necessary environments.
* AfterAction - Executed after the application is created, but before the `Engine` is launched.

```puml
@startuml
title Bootloader Process

start
group Bootloader Build
  :Loading application configuration;
  :Loading Bootloader classes;
end group
group Bootloader Execution
  :Before Action: Prepare System Environment;
  :Build Application: Create Application and Environments;
  :After Action: Prepare Engine;
end group
:Engine Launch;
stop

@enduml
```

Once the `Engine` has started, the initialization process is considered complete. The application is loaded into memory and is ready to operate.

## Component Installation

From the application's perspective, a component is considered installed if it has registered itself with the `Bootloader` 
and is therefore available to other components through `Dependency Injection`.

Since components are most often developed as `Composer` packages, 
their installation process is handled by a special installer that extends Composer.

