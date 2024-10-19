<?php

namespace App\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class FlagToFileTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if (is_string($value) && file_exists($value)) {
            return new File($value);
        }
        return null;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if ($value instanceof File) {
            return $value->getPathname();
        }
        return null;
    }
}
