<?php

namespace App\Interfaces;

interface FormatterInterface
{
    /**
     * @param string|null $file
     * @return mixed
     */
    public function formatterData(string $file = null);
}
