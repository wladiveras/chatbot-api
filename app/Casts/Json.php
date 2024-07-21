<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Json implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        // Check if the value is a string that could be JSON encoded
        if (is_string($value)) {
            $jsonString = $value[0];

            // Step 2: Remove the additional escaping of quotes
            $jsonString = stripslashes($jsonString);

            // Step 3: Decode the JSON string to convert it into a PHP array
            $jsonArray = json_decode($jsonString, true);

            // Check for errors in decoding
            if (json_last_error() === JSON_ERROR_NONE) {
                // Successfully decoded
                print_r($jsonArray);
            } else {
                echo 'Error decoding JSON: '.json_last_error_msg();
            }
        }

        // Encode the value as JSON for storage
        return (array) json_encode($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return json_encode($value);
    }
}
