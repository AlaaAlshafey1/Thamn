<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => $this->image ? asset('storage/' . $this->image) : null,

            'is_active' => (bool) $this->is_active,
            'is_verified' => (bool) $this->is_verified,
            'role_id' => $this->role_id,

            'fcm_token_android' => $this->fcm_token_android,
            'fcm_token_ios' => $this->fcm_token_ios,
            'fcm_token' => $this->fcm_token,
            'device_type' => $this->device_type,

            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
