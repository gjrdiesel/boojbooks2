<?php

namespace App\Service;

interface BookApiInterface
{
    function search(string $query, $args = []);
}
