<?php

function pluck($array, $key)
{
    return array_map(
        function($element) use ($key) {
            if (is_array($element)) {
                return $element[$key];
            } else {
                return $element->$key;
            }
        }
    , $array);
}
