#!/bin/bash

benchmarks="DenormalizeSmallBench
DenormalizeArticleBench
TheOtherWaySmallBench
TheOtherWayArticleBench"

for filter in $benchmarks; do
    echo "Running ${filter}";
    vendor/bin/phpbench run \
        --report 'generator: "table", sort: {benchmark: "asc", mean: "asc"}' \
        --iterations=15 \
        --revs=15 \
        --filter="${filter}";
done

