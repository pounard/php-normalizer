# Experimental PHP object normalizer

Data normalizer that is built over a very strict (de)normalization process
aiming the following goals:

 - be liberal in what you accept,

 - be converservative in what you send,

 - strict typing of everything, attempt to avoid dangling mixed type
   whenever it is possible,

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

# Project status

Is remain an experimental project until the following features are reached:

 - [ ] better reflection discovery using typings directly from PHP whenver
       possible,

 - [ ] better API to replace the context factory,

 - [x] symfony normalizer proxy,

 - [ ] symfony context options support for transparent compatibility,

 - [ ] symfony bundle for autoconfiguration,

 - [ ] implement a few more custom normalizers (SPL files, ...),

 - [ ] user configuration merging with reflection determined definitions,

 - [ ] user configuration documentation

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

Default normalizer and denormalizer are rather simple. More documentation to
come about design choices.

# Benchmarks

In order to run benchmarks:

```sh
vendor/bin/phpbench run --report 'generator: "table"'
```

Run them with XDebug profiler enable (warning this will create a huge lot of
profiling files):

```sh
XDEBUG_CONFIG="remote_enable=0 profiler_enable=1" vendor/bin/phpbench run --report 'generator: "table"' --revs 5
```

Arbitrary recent benchmark run result on php 7.3:

```
+-------------------------+-------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+-------+
| benchmark               | subject           | tag | groups | params | revs | its | mem_peak   | best        | mean        | mode        | worst       | stdev     | rstdev | diff  |
+-------------------------+-------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+-------+
| DenormalizeSmallBench   | benchMap          |     |        | []     | 50   | 30  | 5,192,544b | 388.080μs   | 441.399μs   | 412.831μs   | 622.300μs   | 61.055μs  | 13.83% | 1.00x |
| DenormalizeSmallBench   | benchReflection   |     |        | []     | 50   | 30  | 5,194,544b | 457.700μs   | 559.970μs   | 521.489μs   | 680.960μs   | 59.857μs  | 10.69% | 1.27x |
| DenormalizeSmallBench   | benchSymfony      |     |        | []     | 50   | 30  | 5,760,064b | 1,273.800μs | 1,514.301μs | 1,417.402μs | 2,000.340μs | 168.223μs | 11.11% | 3.43x |
| DenormalizeSmallBench   | benchSymfonyProxy |     |        | []     | 50   | 30  | 5,195,872b | 524.120μs   | 660.347μs   | 616.755μs   | 874.760μs   | 99.054μs  | 15.00% | 1.50x |
| DenormalizeArticleBench | benchMap          |     |        | []     | 50   | 30  | 5,588,957b | 1,293.280μs | 1,566.911μs | 1,501.545μs | 2,516.540μs | 223.553μs | 14.27% | 3.55x |
| DenormalizeArticleBench | benchReflection   |     |        | []     | 50   | 30  | 5,539,357b | 1,197.040μs | 1,454.793μs | 1,436.956μs | 2,003.600μs | 178.016μs | 12.24% | 3.30x |
| DenormalizeArticleBench | benchSymfony      |     |        | []     | 50   | 30  | 6,261,429b | 3,583.400μs | 4,147.278μs | 3,748.492μs | 5,378.340μs | 507.634μs | 12.24% | 9.40x |
| DenormalizeArticleBench | benchSymfonyProxy |     |        | []     | 50   | 30  | 5,577,869b | 1,278.560μs | 1,542.009μs | 1,387.594μs | 2,063.300μs | 218.326μs | 14.16% | 3.49x |
| NormalizeSmallBench     | benchMap          |     |        | []     | 50   | 30  | 5,189,528b | 401.880μs   | 462.149μs   | 427.146μs   | 670.860μs   | 70.720μs  | 15.30% | 1.05x |
| NormalizeSmallBench     | benchReflection   |     |        | []     | 50   | 30  | 5,191,528b | 425.380μs   | 496.917μs   | 454.736μs   | 774.240μs   | 84.297μs  | 16.96% | 1.13x |
| NormalizeSmallBench     | benchSymfony      |     |        | []     | 50   | 30  | 5,331,672b | 444.020μs   | 593.995μs   | 582.936μs   | 785.300μs   | 89.590μs  | 15.08% | 1.35x |
| NormalizeSmallBench     | benchSymfonyProxy |     |        | []     | 50   | 30  | 5,192,664b | 568.740μs   | 696.881μs   | 642.490μs   | 1,034.000μs | 117.794μs | 16.90% | 1.58x |
| NormalizeArticleBench   | benchMap          |     |        | []     | 50   | 30  | 5,536,468b | 773.760μs   | 937.800μs   | 961.987μs   | 1,134.000μs | 94.543μs  | 10.08% | 2.12x |
| NormalizeArticleBench   | benchReflection   |     |        | []     | 50   | 30  | 5,540,820b | 998.360μs   | 1,097.254μs | 1,069.148μs | 1,363.740μs | 82.882μs  | 7.55%  | 2.49x |
| NormalizeArticleBench   | benchSymfony      |     |        | []     | 50   | 30  | 5,706,146b | 1,781.240μs | 2,077.023μs | 2,147.542μs | 2,420.080μs | 169.943μs | 8.18%  | 4.71x |
| NormalizeArticleBench   | benchSymfonyProxy |     |        | []     | 50   | 30  | 5,578,860b | 984.100μs   | 1,085.477μs | 1,138.627μs | 1,238.820μs | 75.483μs  | 6.95%  | 2.46x |
+-------------------------+-------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+-------+
```

