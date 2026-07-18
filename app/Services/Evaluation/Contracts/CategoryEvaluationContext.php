<?php

namespace App\Services\Evaluation\Contracts;

/**
 * Interface for category-specific AI evaluation context.
 *
 * To add a new category:
 * 1. Create a new class in App\Services\Evaluation\Contexts
 * 2. Implement this interface
 * 3. Register it in CategoryContextResolver
 *
 * No existing code needs to be modified (Open/Closed Principle).
 */
interface CategoryEvaluationContext
{
    /**
     * Keywords (Arabic & English) used to auto-match this context to a category name.
     */
    public static function getKeywords(): array;

    /**
     * The AI's role description for this category.
     */
    public function getRole(): string;

    /**
     * Market reference sources the AI should use for pricing.
     */
    public function getMarketReferences(): string;

    /**
     * Category-specific pricing tips and evaluation criteria.
     */
    public function getPricingTips(): string;

    /**
     * Tips for evaluating when no images are provided.
     */
    public function getNoImageDataTips(): string;

    /**
     * Tips for analyzing uploaded images specific to this category.
     */
    public function getImageAnalysisTips(): string;
}
