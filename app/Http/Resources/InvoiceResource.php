<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->order->category->name_ar ?? $this->order->category->name_en ?? 'فاتورة تقييم',
            'date' => $this->created_at->format('Y-m-d'),
            'invoice_number' => 'INV-' . str_pad($this->id, 3, '0', STR_PAD_LEFT),
            'amount' => (float) $this->amount,
            'status' => $this->mapStatus($this->status),
        ];
    }

    /**
     * Map payment status to invoice status
     * 
     * @param string $status
     * @return string
     */
    private function mapStatus($status): string
    {
        $map = [
            'paid' => 'paid',
            'INITIATED' => 'pending',
            'failed' => 'cancelled',
        ];

        return $map[$status] ?? 'pending';
    }
}
