<?php

namespace App\Validators;

use Core\Validators\BaseValidator;

class ProductValidator extends BaseValidator
{
    /**
     * Validation rules for creating a product.
     *
     * @return array
     */

    public static function validateCreate(): array
    {
        return [
            'sku'            => 'required|string|max:225|unique:products,sku',
            'name'           => 'required|string|max:255',
            'price'          => 'required|integer|min:0',
            'quantity'       => 'nullable|integer|min:0',
            'content'        => 'nullable|string',
            'summary'        => 'nullable|string',
            'image'          => 'nullable|string|max:255',
            'images'         => 'nullable|string',
            'average_rating' => 'nullable|numeric|min:0|',
            'description'    => 'nullable|string',
            'alias'          => 'nullable|string|max:255|',
            'status'         => 'nullable|integer|in:0,1',
            'category_id'    => 'nullable|integer|',
            'brand_id'       => 'nullable|integer|',
            'supplier_id'    => 'nullable|integer|',
        ];
    }

    /**
     * Summary of validateUpdate
     * @param int $id
     */
    public static function validateUpdate(int $id): array
    {
        return [
            'sku'            => 'required|string|max:225|unique:products,sku,' . $id,
            'name'           => 'required|string|max:255',
            'price'          => 'required|integer|min:0',
            'quantity'       => 'nullable|integer|min:0',
            'content'        => 'nullable|string',
            'summary'        => 'nullable|string',
            'image'          => 'nullable|string|max:255',
            'images'         => 'nullable|string',
            'average_rating' => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'alias'          => 'nullable|string|max:255|',
            'status'         => 'nullable|integer|in:0,1',
            'category_id'    => 'nullable|integer|',
            'brand_id'       => 'nullable|integer|',
            'supplier_id'    => 'nullable|integer|',
        ];
    }
}
