<?php
// database/factories/MedicineFactory.php
namespace Database\Factories;

use App\Models\Medicine;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineFactory extends Factory
{
    protected $model = Medicine::class;
    
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'generic_name' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'description' => $this->faker->sentence(),
            'purchase_price' => $this->faker->randomFloat(2, 1, 100),
            'selling_price' => $this->faker->randomFloat(2, 2, 200),
            'quantity' => $this->faker->numberBetween(0, 200),
            'reorder_level' => $this->faker->numberBetween(5, 20),
            'expiry_date' => $this->faker->dateTimeBetween('now', '+2 years'),
            'batch_number' => 'BATCH' . $this->faker->unique()->numberBetween(1000, 9999),
            'shelf_number' => $this->faker->randomElement(['A1', 'A2', 'B1', 'B2', 'C1', 'C2']),
            'requires_prescription' => $this->faker->boolean(30)
        ];
    }
}