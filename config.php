<?php

/**
 * Read an environment value with a default fallback.
 *
 * @param string $key Environment variable name.
 * @param string $default Value used when the variable is not set.
 * @return string Resolved configuration value.
 */
function env_value(string $key, string $default): string {
    $value = getenv($key);
    return $value === false || $value === '' ? $default : $value;
}

define('USERNAME', env_value('DB_USERNAME', 'docker'));
define('PASSWORD', env_value('DB_PASSWORD', 'docker'));
define('HOST', env_value('DB_HOST', 'db'));
define('DATABASE', env_value('DB_NAME', 'db'));
