<?php

namespace App\Jobs;

use App\Mail\PriceChangeNotification;
use App\Models\Product;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPriceChangeNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected $product, protected $oldPrice, protected $newPrice, protected $email)
    {
        //
    }

    public static function getEmailNotification(): string
    {
        return config()->string('appfront.products.price_notification_email');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)
            ->send(new PriceChangeNotification(
                $this->product,
                $this->oldPrice,
                $this->newPrice
            ));
    }

    public static function forProduct(Product $product, $oldPrice): ?Exception
    {
        $notificationEmail = self::getEmailNotification();

        try {
            self::dispatch(
                $product,
                $oldPrice,
                $product->price,
                $notificationEmail
            );

            return null;
        } catch (Exception $e) {
            return $e;
        }
    }
}
