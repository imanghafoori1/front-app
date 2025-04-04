<?php

namespace App\Console\Commands;

use App\Jobs\SendPriceChangeNotification;
use App\Models\Product;
use Exception;
use Illuminate\Console\Command;

class UpdateProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update {id} {--name=} {--description=} {--price=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a product with the specified details';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');
        $product = Product::find($id);

        if (is_null($product)) {
            $this->error('Product not found with id: '.$id);

            return Command::FAILURE;
        }

        $data = [];
        if ($this->option('name')) {
            $data['name'] = $this->option('name');
            if (empty($data['name']) || trim($data['name']) == '') {
                $this->error('Name cannot be empty.');

                return Command::FAILURE;
            }
            if (strlen($data['name']) < 3) {
                $this->error('Name must be at least 3 characters long.');

                return Command::FAILURE;
            }
        }
        if ($this->option('description')) {
            $data['description'] = $this->option('description');
        }
        if ($this->option('price')) {
            $data['price'] = $this->option('price');
        }

        if (! empty($data)) {
            $product->fill($data);

            $this->info('Product updated successfully.');

            // Check if price has changed
            if ($product->isDirty('price')) {
                $this->printPriceChange($product);
                $result = SendPriceChangeNotification::forProduct($product);
                $this->handleResults($result);
            }
            $product->save();
        } else {
            $this->info('No changes provided. Product remains unchanged.');
        }

        return Command::SUCCESS;
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

    private function printPriceChange(Product $product): void
    {
        $this->info("Price changed from {$product->getOriginal('price')} to {$product->price}.");
    }
}
