# Experimental PHP object normalizer

Data normalizer that is built over a very strict (de)normalization process
aiming the following goals:

 - be liberal in what you accept,

 - be converservative in what you send,

 - strict typing of everything, do not let dangling mixed type
   meander,

 - do not rely on heavy magic, in opposition to what
   symfony/serializer does,

 - allow any weird objects, private properties, private constructors,
   immutable data structures and all other use case we could encounter
   to work gracefully throught this component,

 - allow partial objects to be hydrated in case of partial or
   broken input,

 - property group support, partial object extraction and denormalization,

 - property and type alias support and easy configuration, allowing you to
   work with this component in aging and evolving applications ensuring
   maximum compatibility with legacy data or external application interfaces,

 - do not ever crash in case of error,

 - let any external validation layer to do its own job, this component will
   never enforce any kind of validation,

 - be as fast as possible, but features and resilience always come at some cost.

# Components

## Types definitions map

Types definitions along with their aliases, properties definitions and aliases
is an immutable, front cached giant hashmap behaving as a key-value store that
holds every information on types we need to build a (de)normalization plan.

The types definitions map is alimented by side components that need to be
explicitely setup:

 - most basic one is a plain PHP array containing the whole configuration,
   suitable for caching as a generated PHP files,

 - a YAML definition mecanism, iso with the PHP version, alowing for human
   editing for types definitions,

 - a dynamic at runtime configuration pass using the reflection API and
   symfony/property-info component if available,

 - a definition chain and merge implementation allowing all of those
   implementations to live side by side,

 - front cache implementation to live independently of those implementations.

This component has no dependency on any other functionnal domain of this API.

## Type definition builder

Based on the type definition map interfaces, it provides implementations able
to introspect for data types.

Each of these implementations might have as many external dependencies as they
need for completing its task.

## Hydrator

Hydrator component must be fast, and should never call object constructors nor
attempt any validation, it's basically a component that just set values onto
object properties.

Hydrator component should always be stateless and context-free. It handles:

 - at normalization, the very first operation which is extracting an object
   values as a not yet normalized array, upon which the normalizer will be
   able to work on,

 - at denormalization, the very last operation which is injecting the
   denormalized values into the object.

An external component is used, it has no dependency on any other functionnal
domain of this API.

## Context

Context is basically an option container, along with a few helpers for hanlding
recursivity, graph and circular references detection.

Context is also responsible to hold the types definitions reference and feed
the normalizer and denormalizer with types information along its object graph
traversal.

## (De)Normalizer


