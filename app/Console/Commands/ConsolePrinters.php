<?php

namespace App\Console\Commands;

use App\Jobs\SendPriceChangeNotification;
use Exception;

trait ConsolePrinters
{
    private function printNoChange(): void
    {
        $this->info('No changes provided. Product remains unchanged.');
    }

    private function printPriceChange($oldPrice, $newPrice): void
    {
        $this->info("Price changed from $oldPrice to $newPrice.");
    }

    private function handleResults(?Exception $e): void
    {
        if ($e) {
            $this->error('Failed to dispatch price change notification: '.$e->getMessage());
        } else {
            $notificationEmail = SendPriceChangeNotification::getEmailNotification();
            $this->info("Price change notification dispatched to {$notificationEmail}.");
        }
    }
}
