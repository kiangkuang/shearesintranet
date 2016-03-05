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

function featuresCmp($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a['enabled'] > $b['enabled']) ? -1 : 1;
}
