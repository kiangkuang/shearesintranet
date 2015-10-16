<?php

function pluck($array, $key)
{
    $result = [];
    if ($array) {
        foreach ($array as $object) {
            $result[] = $object->$key;
        }
    }

    return $result;
}
