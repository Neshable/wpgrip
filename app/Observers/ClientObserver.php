<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\ClientActivity;

class ClientObserver
{
    public function updated(Client $client): void
    {
        // Log status changes
        if ($client->isDirty('status')) {
            $old = $client->getOriginal('status');
            $new = $client->status?->value ?? $client->getAttribute('status');

            ClientActivity::log(
                $client,
                'status_changed',
                "Status changed from {$old} to {$new}",
                null,
                ['old' => $old, 'new' => $new]
            );
        }

        // Log contract changes
        if ($client->isDirty('contract_end') && $client->contract_end) {
            ClientActivity::log(
                $client,
                'contract_updated',
                'Contract end date set to ' . $client->contract_end->format('M d, Y'),
            );
        }

        // Log monthly value changes
        if ($client->isDirty('monthly_value') && $client->monthly_value) {
            $old = $client->getOriginal('monthly_value');
            ClientActivity::log(
                $client,
                'revenue_updated',
                "Monthly value updated from {$old} to {$client->monthly_value} {$client->currency}",
                null,
                ['old' => $old, 'new' => $client->monthly_value]
            );
        }
    }

    public function created(Client $client): void
    {
        ClientActivity::log(
            $client,
            'client_created',
            'Client created',
        );
    }
}
