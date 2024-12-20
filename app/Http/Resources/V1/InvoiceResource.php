<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    private array $types = ['C' => 'Cartão', 'B' => 'Boleto', 'P' => 'Pix'];

    public function toArray(Request $request): array
    {
        $paid = $this->paid;
        return [
            'user' => [
                'fistName' => $this->user->firstName,
                'lastName' => $this->user->lastName,
                'fullName' => $this->user->firstName . ' ' . $this->lastName,
                'email' => $this->user->email  
            ],
            'type' => $this->types[$this->type],
            'value' => 'R$ '. number_format($this->value, 2, ',', '.'),
            'paid' => $paid ? 'Pago' : 'Não pago',
            'payment_date' => $paid ? Carbon::parse($this->payment_date)->format('d/m/Y H:i:s') : Null,
            'payment_since' => $paid ? Carbon::parse($this->payment_date)->diffForHumans() : Null,
        ];
    }
}
