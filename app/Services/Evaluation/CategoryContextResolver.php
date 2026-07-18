<?php

namespace App\Services\Evaluation;

use App\Services\Evaluation\Contracts\CategoryEvaluationContext;
use App\Services\Evaluation\Contexts\CarsContext;
use App\Services\Evaluation\Contexts\PhonesContext;
use App\Services\Evaluation\Contexts\RealEstateContext;
use App\Services\Evaluation\Contexts\WatchesContext;
use App\Services\Evaluation\Contexts\FurnitureContext;
use App\Services\Evaluation\Contexts\ElectronicsContext;
use App\Services\Evaluation\Contexts\DefaultContext;

class CategoryContextResolver
{
    /**
     * @var string[]
     */
    protected array $registeredContexts = [
        CarsContext::class,
        PhonesContext::class,
        RealEstateContext::class,
        WatchesContext::class,
        FurnitureContext::class,
        ElectronicsContext::class,
    ];

    /**
     * Resolve the appropriate evaluation context based on the category names.
     */
    public function resolve(string $categorySlug, string $categoryAr): CategoryEvaluationContext
    {
        $categoryLower = mb_strtolower($categorySlug);
        $categoryArLower = mb_strtolower($categoryAr);

        foreach ($this->registeredContexts as $contextClass) {
            $keywords = $contextClass::getKeywords();
            
            foreach ($keywords as $keyword) {
                if (str_contains($categoryArLower, $keyword) || str_contains($categoryLower, $keyword)) {
                    return new $contextClass();
                }
            }
        }

        // If no match is found, return the default context with the category name
        return new DefaultContext($categoryAr);
    }
}
