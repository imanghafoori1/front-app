<?php

namespace App\Console\Commands;

use App\Jobs\SendPriceChangeNotification;
use App\Models\Product;
use Illuminate\Console\Command;

class UpdateProduct extends Command
{
    use ConsolePrinters;

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
        $product = Product::query()->find($id);

        if (is_null($product)) {
            $this->error('Product not found with id: '.$id);

            return Command::FAILURE;
        }

        $data = $this->readInputs();

        if ($data['name']) {
            $error = $this->checkNameOption($data['name']);
            if ($error) {
                $this->error($error);

                return Command::FAILURE;
            }
        }

        $data = array_filter($data);

        $data ? $this->handleData($product, $data) : $this->printNoChange();

        return Command::SUCCESS;
    }

    private function handleData($product, array $data): void
    {
        $product->fill($data);

        $this->info('Product updated successfully.');

        // Check if price has changed
        if ($product->isDirty('price')) {
            $this->printPriceChange($product->getOriginal('price'), $product->price);
            $result = SendPriceChangeNotification::forProduct($product);
            $this->handleResults($result);
        }
        $product->save();
    }

    private function checkNameOption($name)
    {
        if (empty($name) || trim($name) == '') {
            return 'Name cannot be empty.';
        }

        if (strlen($name) < 3) {
            return 'Name must be at least 3 characters long.';
        }
    }

    private function readInputs(): array
    {
        return [
            'name' => $this->option('name'),
            'description' => $this->option('description'),
            'price' => $this->option('price'),
        ];
    }
}
