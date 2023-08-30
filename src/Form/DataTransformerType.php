<?php

namespace App\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToFileTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if ($value instanceof File) {
            return $value;
        }
        return null;
    }

    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return new File($value);
        } catch (\Exception $e) {
            throw new TransformationFailedException('The file cannot be processed properly.');
        }
    }
}
