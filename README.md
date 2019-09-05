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

 - [x] symfony bundle for autoconfiguration,

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

# Methodology

This API was orignally built from a functionnal pure pseudo-code working
algorithm, which was then converted to an object oriented, interface based
API more suitable for existing frameworks.

Because the idea of generating (de)normalizers was growing, a new approach
has been taken:

 - in the src/Generated folder, the same code exists in many versions, side
   by side, each version being a new evolution from the previous,

 - iteration 1 code is the pseudo yet working functional code, existing purely
   as a proof of concept,

 - iteration 2 attempts a naive version of code generation,

 - iteration 3 is a refined version of iteration 1, using external function
   helpers for dealing with scalar types and error handling,

 - iteration 4 is a refined version of iteration 2 which generates code that
   uses iteration 3 helpers, and much more readable and debugable code that
   the previous iteration.

More iterations will come, a few things are still missing in generated code:

 - normalization has not been implemented, only denormalization,
 - direct calls to other existing generated denormalizers,
 - ability to plug custom arbitrary (de)normalizers for when a type is
   unknown,
 - restore it into a object oriented API,
 - unit tests.

# Benchmarks

Benchmarks compare Symfony implementation vs this API implementation VS all
iterations verions.

In order to run benchmarks:

```sh
vendor/bin/phpbench run --report 'generator: "table", sort: {benchmark: "asc", mean: "asc"}'
```

Run them with XDebug profiler enable (warning this will create a huge lot of
profiling files):

```sh
XDEBUG_CONFIG="remote_enable=0 profiler_enable=1" vendor/bin/phpbench run --report 'generator: "table"' --revs 5
```

Compare only denormalization:

```sh
vendor/bin/phpbench run --report 'generator: "table", sort: {benchmark: "asc", mean: "asc"}' --iterations=10 --revs=10 --filter=Denorm
```

Arbitrary recent benchmark run result on php 7.3:

```
+-------------------------+---------------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+--------+
| benchmark               | subject                   | tag | groups | params | revs | its | mem_peak   | best        | mean        | mode        | worst       | stdev     | rstdev | diff   |
+-------------------------+---------------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+--------+
| DenormalizeSmallBench   | benchIteration1WithCache  |     |        | []     | 50   | 30  | 6,003,856b | 722.860μs   | 1,174.854μs | 872.595μs   | 3,191.080μs | 658.964μs | 56.09% | 5.71x  |
| DenormalizeSmallBench   | benchIteration1           |     |        | []     | 50   | 30  | 5,379,800b | 564.340μs   | 724.789μs   | 650.635μs   | 1,185.420μs | 143.227μs | 19.76% | 3.52x  |
| DenormalizeSmallBench   | benchIteration2WithCache  |     |        | []     | 50   | 30  | 6,003,856b | 403.380μs   | 462.533μs   | 427.998μs   | 535.800μs   | 40.850μs  | 8.83%  | 2.25x  |
| DenormalizeSmallBench   | benchIteration2           |     |        | []     | 50   | 30  | 5,396,744b | 234.660μs   | 263.428μs   | 244.058μs   | 321.980μs   | 23.872μs  | 9.06%  | 1.28x  |
| DenormalizeSmallBench   | benchIteration3WithCache  |     |        | []     | 50   | 30  | 6,003,856b | 756.440μs   | 927.259μs   | 821.391μs   | 1,704.720μs | 233.704μs | 25.20% | 4.51x  |
| DenormalizeSmallBench   | benchIteration3           |     |        | []     | 50   | 30  | 5,379,800b | 606.520μs   | 719.647μs   | 689.143μs   | 1,636.280μs | 177.310μs | 24.64% | 3.50x  |
| DenormalizeSmallBench   | benchIteration4WithCache  |     |        | []     | 50   | 30  | 6,003,856b | 359.740μs   | 426.945μs   | 396.987μs   | 682.340μs   | 61.972μs  | 14.52% | 2.08x  |
| DenormalizeSmallBench   | benchIteration4           |     |        | []     | 50   | 30  | 5,390,520b | 186.880μs   | 205.682μs   | 197.821μs   | 342.180μs   | 28.081μs  | 13.65% | 1.00x  |
| DenormalizeSmallBench   | benchCustomWithConfig     |     |        | []     | 50   | 30  | 5,427,184b | 454.440μs   | 477.985μs   | 473.571μs   | 509.360μs   | 12.923μs  | 2.70%  | 2.32x  |
| DenormalizeSmallBench   | benchCustomWithReflection |     |        | []     | 50   | 30  | 6,004,528b | 668.000μs   | 815.374μs   | 779.836μs   | 1,269.560μs | 133.475μs | 16.37% | 3.96x  |
| DenormalizeSmallBench   | benchSymfony              |     |        | []     | 50   | 30  | 6,055,336b | 1,128.480μs | 1,263.127μs | 1,235.948μs | 1,627.720μs | 101.063μs | 8.00%  | 6.14x  |
| DenormalizeSmallBench   | benchSymfonyProxy         |     |        | []     | 50   | 30  | 6,005,304b | 707.380μs   | 796.639μs   | 826.676μs   | 877.660μs   | 48.916μs  | 6.14%  | 3.87x  |
| DenormalizeArticleBench | benchIteration1WithCache  |     |        | []     | 50   | 30  | 6,486,093b | 2,761.260μs | 3,166.854μs | 2,963.546μs | 4,711.600μs | 489.760μs | 15.47% | 15.40x |
| DenormalizeArticleBench | benchIteration1           |     |        | []     | 50   | 30  | 5,748,895b | 1,578.460μs | 1,724.799μs | 1,681.579μs | 2,048.940μs | 113.624μs | 6.59%  | 8.39x  |
| DenormalizeArticleBench | benchIteration2           |     |        | []     | 50   | 30  | 5,789,850b | 841.240μs   | 1,051.113μs | 947.955μs   | 1,750.100μs | 245.442μs | 23.35% | 5.11x  |
| DenormalizeArticleBench | benchIteration3WithCache  |     |        | []     | 50   | 30  | 6,486,055b | 2,830.560μs | 2,992.171μs | 2,928.322μs | 3,325.180μs | 128.881μs | 4.31%  | 14.55x |
| DenormalizeArticleBench | benchIteration3           |     |        | []     | 50   | 30  | 5,749,098b | 1,594.320μs | 1,902.089μs | 1,744.422μs | 2,964.000μs | 353.609μs | 18.59% | 9.25x  |
| DenormalizeArticleBench | benchIteration4           |     |        | []     | 50   | 30  | 5,774,577b | 720.520μs   | 820.649μs   | 821.035μs   | 972.300μs   | 57.285μs  | 6.98%  | 3.99x  |
| DenormalizeArticleBench | benchCustomWithConfig     |     |        | []     | 50   | 30  | 5,823,098b | 1,246.180μs | 1,368.924μs | 1,372.057μs | 1,579.460μs | 77.426μs  | 5.66%  | 6.66x  |
| DenormalizeArticleBench | benchCustomWithReflection |     |        | []     | 50   | 30  | 6,486,880b | 2,335.140μs | 2,671.507μs | 2,474.711μs | 3,993.760μs | 403.226μs | 15.09% | 12.99x |
| DenormalizeArticleBench | benchSymfony              |     |        | []     | 50   | 30  | 6,563,819b | 2,692.000μs | 2,864.325μs | 2,767.577μs | 3,420.680μs | 156.726μs | 5.47%  | 13.93x |
| DenormalizeArticleBench | benchSymfonyProxy         |     |        | []     | 50   | 30  | 6,490,979b | 2,421.720μs | 2,735.031μs | 2,545.931μs | 4,610.400μs | 550.397μs | 20.12% | 13.30x |
| NormalizeSmallBench     | benchMap                  |     |        | []     | 50   | 30  | 5,414,664b | 400.200μs   | 463.751μs   | 423.487μs   | 616.560μs   | 50.267μs  | 10.84% | 2.25x  |
| NormalizeSmallBench     | benchReflection           |     |        | []     | 50   | 30  | 5,992,896b | 589.100μs   | 698.739μs   | 646.449μs   | 946.480μs   | 89.080μs  | 12.75% | 3.40x  |
| NormalizeSmallBench     | benchSymfony              |     |        | []     | 50   | 30  | 5,552,296b | 374.500μs   | 502.091μs   | 421.526μs   | 1,125.440μs | 172.337μs | 34.32% | 2.44x  |
| NormalizeSmallBench     | benchSymfonyProxy         |     |        | []     | 50   | 30  | 5,993,336b | 658.500μs   | 766.249μs   | 790.622μs   | 1,019.540μs | 75.024μs  | 9.79%  | 3.73x  |
| NormalizeArticleBench   | benchMap                  |     |        | []     | 50   | 30  | 5,798,291b | 681.140μs   | 813.481μs   | 768.356μs   | 992.800μs   | 82.251μs  | 10.11% | 3.96x  |
| NormalizeArticleBench   | benchReflection           |     |        | []     | 50   | 30  | 6,410,878b | 1,995.640μs | 2,337.365μs | 2,154.886μs | 4,461.540μs | 509.888μs | 21.81% | 11.36x |
| NormalizeArticleBench   | benchSymfony              |     |        | []     | 50   | 30  | 5,986,809b | 1,407.340μs | 1,550.210μs | 1,480.062μs | 1,806.920μs | 95.754μs  | 6.18%  | 7.54x  |
| NormalizeArticleBench   | benchSymfonyProxy         |     |        | []     | 50   | 30  | 6,413,746b | 2,115.560μs | 2,400.141μs | 2,288.178μs | 3,472.600μs | 315.005μs | 13.12% | 11.67x |
+-------------------------+---------------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+--------+
```

Please note that the *WithCache* suffix is confusing, it actually points to
implementations using runtime reflection to determine types.

