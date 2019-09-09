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

## Running benchmarks

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

## Arbitrary recent benchmark run result on php 7.3:

Please note that all results are to be taken with prudence: some of the
iterations don't lead to coherent data, Symfony itself misbehave sometimes.

Also you have to note that Symfony implementation is not bootstrapped using
full caches, as it would within a Symfony application in production mode,
nevertheless you have to take into account that:

 - class discrimination is not setup, it makes it faster,
 - no annotations were used, and Doctrine annotations are not setup, it should
   also make it faster,
 - tested use cases are not complex, there is no nested objects, only one
   in the "article" tests.

Implementations that works fine are:

 - Iteration1
 - Iteration7
 - Custom

Because normalizers were very, very fast to implement after the denormalizer
skeleton was fully written, going throught all iterations for normalization
was unnecessary, only iterations 1 and 7 are implemented.

### Denormalizing very small objects

```
+-----------------------+-------------------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+-------+
| benchmark             | subject                       | tag | groups | params | revs | its | mem_peak   | best        | mean        | mode        | worst       | stdev     | rstdev | diff  |
+-----------------------+-------------------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+-------+
| DenormalizeSmallBench | benchIteration4WithConfigOnly |     |        | []     | 15   | 15  | 6,574,808b | 375.333μs   | 387.516μs   | 380.785μs   | 471.400μs   | 23.159μs  | 5.98%  | 1.00x |
| DenormalizeSmallBench | benchIteration4WithReflection |     |        | []     | 15   | 15  | 6,574,808b | 375.800μs   | 393.218μs   | 383.998μs   | 464.933μs   | 24.479μs  | 6.23%  | 1.01x |
| DenormalizeSmallBench | benchIteration2WithReflection |     |        | []     | 15   | 15  | 6,576,832b | 415.133μs   | 426.733μs   | 428.398μs   | 445.667μs   | 7.848μs   | 1.84%  | 1.10x |
| DenormalizeSmallBench | benchIteration5WithConfigOnly |     |        | []     | 15   | 15  | 6,574,544b | 405.667μs   | 429.862μs   | 417.228μs   | 515.067μs   | 27.733μs  | 6.45%  | 1.11x |
| DenormalizeSmallBench | benchIteration2WithConfigOnly |     |        | []     | 15   | 15  | 6,576,832b | 414.667μs   | 431.213μs   | 424.981μs   | 504.000μs   | 20.994μs  | 4.87%  | 1.11x |
| DenormalizeSmallBench | benchIteration5WithReflection |     |        | []     | 15   | 15  | 6,574,544b | 405.000μs   | 436.693μs   | 416.286μs   | 618.600μs   | 54.919μs  | 12.58% | 1.13x |
| DenormalizeSmallBench | benchIteration7WithReflection |     |        | []     | 15   | 15  | 6,577,280b | 455.933μs   | 475.782μs   | 467.184μs   | 521.267μs   | 20.208μs  | 4.25%  | 1.23x |
| DenormalizeSmallBench | benchIteration6WithConfigOnly |     |        | []     | 15   | 15  | 6,574,920b | 464.000μs   | 483.707μs   | 474.056μs   | 568.867μs   | 26.478μs  | 5.47%  | 1.25x |
| DenormalizeSmallBench | benchIteration7WithConfigOnly |     |        | []     | 15   | 15  | 6,577,280b | 453.267μs   | 486.498μs   | 464.297μs   | 601.600μs   | 45.110μs  | 9.27%  | 1.26x |
| DenormalizeSmallBench | benchIteration6WithReflection |     |        | []     | 15   | 15  | 6,574,920b | 462.667μs   | 495.360μs   | 476.258μs   | 673.133μs   | 52.797μs  | 10.66% | 1.28x |
| DenormalizeSmallBench | benchCustomWithConfigOnly     |     |        | []     | 15   | 15  | 6,607,272b | 630.733μs   | 669.178μs   | 650.222μs   | 811.800μs   | 49.216μs  | 7.35%  | 1.73x |
| DenormalizeSmallBench | benchIteration1WithConfigOnly |     |        | []     | 15   | 15  | 6,560,080b | 844.333μs   | 875.778μs   | 856.814μs   | 964.667μs   | 38.487μs  | 4.39%  | 2.26x |
| DenormalizeSmallBench | benchSymfony                  |     |        | []     | 15   | 15  | 7,280,624b | 2,021.533μs | 2,150.422μs | 2,130.163μs | 2,338.733μs | 73.215μs  | 3.40%  | 5.55x |
| DenormalizeSmallBench | benchCustomWithReflection     |     |        | []     | 15   | 15  | 7,204,416b | 2,197.133μs | 2,279.236μs | 2,246.225μs | 2,482.200μs | 73.474μs  | 3.22%  | 5.88x |
| DenormalizeSmallBench | benchSymfonyProxy             |     |        | []     | 15   | 15  | 7,205,192b | 2,258.333μs | 2,359.769μs | 2,316.943μs | 2,598.667μs | 96.251μs  | 4.08%  | 6.09x |
| DenormalizeSmallBench | benchIteration1WithReflection |     |        | []     | 15   | 15  | 7,203,744b | 2,439.600μs | 2,528.831μs | 2,469.243μs | 2,783.867μs | 108.108μs | 4.28%  | 6.53x |
+-----------------------+-------------------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+-------+
```

### Normalizing very small objects

```
+-----------------------+-------------------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+-------+
| benchmark             | subject                       | tag | groups | params | revs | its | mem_peak   | best        | mean        | mode        | worst       | stdev     | rstdev | diff  |
+-----------------------+-------------------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+-------+
| TheOtherWaySmallBench | benchIteration7WithConfigOnly |     |        | []     | 15   | 15  | 6,568,912b | 243.933μs   | 262.853μs   | 252.839μs   | 318.533μs   | 20.313μs  | 7.73%  | 1.00x |
| TheOtherWaySmallBench | benchIteration7WithReflection |     |        | []     | 15   | 15  | 6,568,912b | 242.733μs   | 265.267μs   | 250.417μs   | 351.800μs   | 32.839μs  | 12.38% | 1.01x |
| TheOtherWaySmallBench | benchIteration1WithConfigOnly |     |        | []     | 15   | 15  | 6,536,336b | 428.267μs   | 455.916μs   | 447.352μs   | 583.067μs   | 34.837μs  | 7.64%  | 1.73x |
| TheOtherWaySmallBench | benchCustomWithConfigOnly     |     |        | []     | 15   | 15  | 6,596,480b | 456.867μs   | 484.596μs   | 478.795μs   | 520.533μs   | 17.331μs  | 3.58%  | 1.84x |
| TheOtherWaySmallBench | benchSymfony                  |     |        | []     | 15   | 15  | 6,760,336b | 590.133μs   | 618.489μs   | 605.904μs   | 754.600μs   | 39.306μs  | 6.36%  | 2.35x |
| TheOtherWaySmallBench | benchIteration1WithReflection |     |        | []     | 15   | 15  | 7,194,544b | 1,897.867μs | 2,016.329μs | 2,001.948μs | 2,238.800μs | 90.053μs  | 4.47%  | 7.67x |
| TheOtherWaySmallBench | benchCustomWithReflection     |     |        | []     | 15   | 15  | 7,195,216b | 1,950.800μs | 2,132.684μs | 2,044.819μs | 2,713.400μs | 201.825μs | 9.46%  | 8.11x |
| TheOtherWaySmallBench | benchSymfonyProxy             |     |        | []     | 15   | 15  | 7,195,992b | 2,017.800μs | 2,137.627μs | 2,073.905μs | 2,339.933μs | 106.468μs | 4.98%  | 8.13x |
+-----------------------+-------------------------------+-----+--------+--------+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+-------+
```

### Denormalizing large objects with multiple inheritance levels

```
+-------------------------+-------------------------------+-----+--------+--------+------+-----+------------+--------------+--------------+--------------+--------------+-----------+--------+--------+
| benchmark               | subject                       | tag | groups | params | revs | its | mem_peak   | best         | mean         | mode         | worst        | stdev     | rstdev | diff   |
+-------------------------+-------------------------------+-----+--------+--------+------+-----+------------+--------------+--------------+--------------+--------------+-----------+--------+--------+
| DenormalizeArticleBench | benchIteration5WithReflection |     |        | []     | 15   | 15  | 6,624,362b | 879.667μs    | 978.409μs    | 982.655μs    | 1,080.533μs  | 46.233μs  | 4.73%  | 1.00x  |
| DenormalizeArticleBench | benchIteration4WithConfigOnly |     |        | []     | 15   | 15  | 6,624,494b | 930.667μs    | 984.116μs    | 968.178μs    | 1,081.600μs  | 43.452μs  | 4.42%  | 1.01x  |
| DenormalizeArticleBench | benchIteration5WithConfigOnly |     |        | []     | 15   | 15  | 6,624,364b | 936.733μs    | 994.724μs    | 970.229μs    | 1,192.200μs  | 66.027μs  | 6.64%  | 1.02x  |
| DenormalizeArticleBench | benchIteration2WithReflection |     |        | []     | 15   | 15  | 6,634,997b | 1,059.467μs  | 1,132.644μs  | 1,091.771μs  | 1,285.600μs  | 68.522μs  | 6.05%  | 1.16x  |
| DenormalizeArticleBench | benchIteration6WithConfigOnly |     |        | []     | 15   | 15  | 6,624,410b | 1,056.667μs  | 1,166.040μs  | 1,175.607μs  | 1,254.000μs  | 49.585μs  | 4.25%  | 1.19x  |
| DenormalizeArticleBench | benchIteration2WithConfigOnly |     |        | []     | 15   | 15  | 6,635,710b | 1,054.267μs  | 1,166.929μs  | 1,118.013μs  | 1,328.000μs  | 82.964μs  | 7.11%  | 1.19x  |
| DenormalizeArticleBench | benchIteration6WithReflection |     |        | []     | 15   | 15  | 6,624,742b | 1,082.267μs  | 1,185.524μs  | 1,175.837μs  | 1,285.733μs  | 57.043μs  | 4.81%  | 1.21x  |
| DenormalizeArticleBench | benchIteration7WithConfigOnly |     |        | []     | 15   | 15  | 6,658,483b | 1,153.600μs  | 1,248.080μs  | 1,196.183μs  | 1,473.600μs  | 98.204μs  | 7.87%  | 1.28x  |
| DenormalizeArticleBench | benchIteration7WithReflection |     |        | []     | 15   | 15  | 6,658,508b | 1,180.800μs  | 1,335.640μs  | 1,385.584μs  | 1,451.200μs  | 85.723μs  | 6.42%  | 1.37x  |
| DenormalizeArticleBench | benchCustomWithConfigOnly     |     |        | []     | 15   | 15  | 6,670,859b | 1,872.667μs  | 2,073.307μs  | 2,103.217μs  | 2,253.933μs  | 102.006μs | 4.92%  | 2.12x  |
| DenormalizeArticleBench | benchIteration1WithConfigOnly |     |        | []     | 15   | 15  | 6,594,358b | 3,005.600μs  | 3,186.889μs  | 3,195.733μs  | 3,338.333μs  | 98.687μs  | 3.10%  | 3.26x  |
| DenormalizeArticleBench | benchSymfony                  |     |        | []     | 15   | 15  | 7,427,237b | 4,465.267μs  | 4,717.373μs  | 4,629.497μs  | 5,126.067μs  | 197.297μs | 4.18%  | 4.82x  |
| DenormalizeArticleBench | benchCustomWithReflection     |     |        | []     | 15   | 15  | 7,354,583b | 9,834.333μs  | 10,476.747μs | 10,231.877μs | 11,664.467μs | 515.844μs | 4.92%  | 10.71x |
| DenormalizeArticleBench | benchSymfonyProxy             |     |        | []     | 15   | 15  | 7,355,357b | 10,641.133μs | 11,187.507μs | 11,388.919μs | 12,010.733μs | 408.076μs | 3.65%  | 11.43x |
+-------------------------+-------------------------------+-----+--------+--------+------+-----+------------+--------------+--------------+--------------+--------------+-----------+--------+--------+
```

### Normalizing large objects with multiple inheritance levels

```
+-------------------------+-------------------------------+-----+--------+--------+------+-----+------------+--------------+--------------+--------------+--------------+-----------+--------+--------+
| benchmark               | subject                       | tag | groups | params | revs | its | mem_peak   | best         | mean         | mode         | worst        | stdev     | rstdev | diff   |
+-------------------------+-------------------------------+-----+--------+--------+------+-----+------------+--------------+--------------+--------------+--------------+-----------+--------+--------+
| TheOtherWayArticleBench | benchIteration7WithReflection |     |        | []     | 15   | 15  | 6,668,389b | 527.467μs    | 560.587μs    | 554.696μs    | 650.600μs    | 27.561μs  | 4.92%  | 1.00x  |
| TheOtherWayArticleBench | benchIteration7WithConfigOnly |     |        | []     | 15   | 15  | 6,668,254b | 533.400μs    | 572.747μs    | 556.026μs    | 656.400μs    | 35.610μs  | 6.22%  | 1.02x  |
| TheOtherWayArticleBench | benchCustomWithConfigOnly     |     |        | []     | 15   | 15  | 6,645,334b | 1,610.400μs  | 1,753.920μs  | 1,691.582μs  | 1,947.667μs  | 100.245μs | 5.72%  | 3.13x  |
| TheOtherWayArticleBench | benchIteration1WithConfigOnly |     |        | []     | 15   | 15  | 6,605,880b | 1,555.600μs  | 1,886.280μs  | 1,893.617μs  | 2,120.067μs  | 138.489μs | 7.34%  | 3.36x  |
| TheOtherWayArticleBench | benchSymfony                  |     |        | []     | 15   | 15  | 6,839,113b | 2,260.733μs  | 2,359.436μs  | 2,341.765μs  | 2,554.400μs  | 73.247μs  | 3.10%  | 4.21x  |
| TheOtherWayArticleBench | benchCustomWithReflection     |     |        | []     | 15   | 15  | 7,341,492b | 9,413.467μs  | 10,012.391μs | 9,741.095μs  | 10,653.600μs | 418.889μs | 4.18%  | 17.86x |
| TheOtherWayArticleBench | benchSymfonyProxy             |     |        | []     | 15   | 15  | 7,342,292b | 9,802.400μs  | 10,366.564μs | 10,251.729μs | 11,353.800μs | 409.447μs | 3.95%  | 18.49x |
| TheOtherWayArticleBench | benchIteration1WithReflection |     |        | []     | 15   | 15  | 7,341,487b | 10,761.800μs | 11,271.644μs | 11,097.759μs | 12,336.800μs | 399.588μs | 3.55%  | 20.11x |
+-------------------------+-------------------------------+-----+--------+--------+------+-----+------------+--------------+--------------+--------------+--------------+-----------+--------+--------+
```
