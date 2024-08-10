# Architecture

## Overview

This document describes the `Application` pattern of this package.
This pattern is intended for developing a `BackEnd` focused on `API` and remote procedure calls.

The `Application` pattern uses the `Service Manager` pattern, 
forming a unified framework together with it.

## Basic Concepts

```puml
@startuml

package "Application" {
    [Engine] 
    
    package "Environments" {
        [System Environment]
        [Public Environment]
        [Request Environment]
    }
}

note bottom of [System Environment]
Defines dependencies available 
to all components of the system
end note

note left of [Public Environment]
Defines services and dependencies 
available for public use
end note

note right of [Request Environment]
Lives in memory only for the duration of the request.
Contains request-specific data.
end note  

[Engine] <.> [External Systems] : Interaction
[Engine] --> [Public Environment] : Using components
[Engine] ..> [Request Environment] : Request data
[Public Environment] --> [System Environment] : Inheritance

@enduml

```