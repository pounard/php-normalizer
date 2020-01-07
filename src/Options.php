<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Common options.
 */
final class Option
{
    const SERIALIATION_FORMAT = 'format'; // Custom

    // DateInterval
    // const FORMAT_KEY = 'dateinterval_format'; // SF

    // DateTime
    const DATE_FORMAT = 'datetime_format'; // SF
    const DATE_TIMEZONE = 'datetime_timezone'; // SF
}

/**
 * Options that need to be evaluated and sorted to their right class.
 *
 * All those come from symfony/serializer component.
 */
final class __OptionTriage
{
    // Default
    const ALLOW_EXTRA_ATTRIBUTES = 'allow_extra_attributes'; // SF
    const ATTRIBUTES = 'attributes'; // SF
    const CALLBACKS = 'callbacks'; // SF
    const GROUPS = 'groups'; // SF
    const IGNORED_ATTRIBUTES = 'ignored_attributes'; // SF

    // Object
    const EXCLUDE_FROM_CACHE_KEY = 'exclude_from_cache_key'; // SF
}

/**
 * Options specific to the normalization process.
 *
 * Most of those come from symfony/serializer component and are compatible with it.
 */
final class NormalizeOption
{
    // Default
    const CIRCULAR_REFERENCE_HANDLER = 'circular_reference_handler'; // SF
    const CIRCULAR_REFERENCE_LIMIT = 'circular_reference_limit'; // SF
    const CIRCULAR_REFERENCE_LIMIT_COUNTERS = 'circular_reference_limit_counters'; // SF

    // Object
    const DEPTH_KEY_PATTERN = 'depth_%s::%s'; // SF
    const ENABLE_MAX_DEPTH = 'enable_max_depth'; // SF
    const MAX_DEPTH_HANDLER = 'max_depth_handler'; // SF
    const SKIP_NULL_VALUES = 'skip_null_values'; // SF
}

/**
 * Options specific to the denormalization process.
 *
 * Most of those come from symfony/serializer component and are compatible with it.
 */
final class DenormalizeOption
{
    // Default
    const OBJECT_TO_POPULATE = 'object_to_populate'; // SF
    const DEFAULT_CONSTRUCTOR_ARGUMENTS = 'default_constructor_arguments'; // SF

    // Object
    const DISABLE_TYPE_ENFORCEMENT = 'disable_type_enforcement'; // SF
}
