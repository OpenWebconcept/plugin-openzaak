<?php

namespace OWC\Zaaksysteem\Repositories;

abstract class AbstractRepository
{
    /**
     * Add form field values to arguments required for creating a 'Zaak'.
     * Mapping is done by the relation between arguments keys and form fields linkedFieldValueZGWs.
     */
    public function mapArgs(array $args, array $fields, array $entry): array
    {
        foreach ($fields as $field) {
            if (empty($field->linkedFieldValueZGW)) {
                continue;
            }

            $property = rgar($entry, (string)$field->id);

            if (empty($property)) {
                continue;
            }

            if ($field->type === 'date') {
                $property = (new \DateTime($property))->format('Y-m-d');
            }

            $args[$field->linkedFieldValueZGW] = $property;
        }

        return $args;
    }
}
