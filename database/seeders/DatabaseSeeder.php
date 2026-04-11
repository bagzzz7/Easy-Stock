<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Users
        User::create([
            'name' => 'Admin',
            'email' => 'admin@pharmacy.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '09171234567',
            'address' => 'Main Branch, Manila'
        ]);
        
        User::create([
            'name' => 'Staff',
            'email' => 'staff@pharmacy.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '09171234568',
            'address' => 'Main Branch, Manila'
        ]);
        
        // Categories
        $categories = [
            'Analgesics' => 'Pain relievers',
            'Antibiotics' => 'Infection fighters',
            'Antipyretics' => 'Fever reducers',
            'Antihistamines' => 'Allergy medications',
            'Vitamins' => 'Nutritional supplements',
            'Cardiovascular' => 'Heart medications',
            'Gastrointestinal' => 'Digestive system drugs',
            'Respiratory' => 'Asthma and respiratory drugs'
        ];
        
        foreach ($categories as $name => $description) {
            Category::create(['name' => $name]);
        }
        
        // Suppliers
        $suppliers = [
            [
                'name' => 'PharmaCorp Inc.',
                'email' => 'orders@pharmacorp.com',
                'phone' => '(02) 8888-9999',
                'address' => '123 Pharma Street, Makati City',
                'contact_person' => 'Juan Dela Cruz'
            ],
            [
                'name' => 'MediSupply Philippines',
                'email' => 'sales@medisupply.ph',
                'phone' => '(02) 7777-8888',
                'address' => '456 Medical Ave, Quezon City',
                'contact_person' => 'Maria Santos'
            ],
            [
                'name' => 'HealthPlus Distributors',
                'email' => 'info@healthplus.com.ph',
                'phone' => '(02) 6666-7777',
                'address' => '789 Health Blvd, Taguig City',
                'contact_person' => 'Pedro Reyes'
            ]
        ];
        
        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
        
        // Medicines
        $medicines = [
            [
                'name' => 'Paracetamol',
                'generic_name' => 'Acetaminophen',
                'brand' => 'Biogesic',
                'category_id' => 1,
                'supplier_id' => 1,
                'description' => 'For fever and pain relief',
                'purchase_price' => 2.50,
                'selling_price' => 5.00,
                'quantity' => 150,
                'reorder_level' => 20,
                'expiry_date' => Carbon::now()->addYears(2),
                'batch_number' => 'PC2024001',
                'shelf_number' => 'A1',
                'requires_prescription' => false
            ],
            [
                'name' => 'Amoxicillin',
                'generic_name' => 'Amoxicillin Trihydrate',
                'brand' => 'Amoxil',
                'category_id' => 2,
                'supplier_id' => 2,
                'description' => 'Antibiotic for bacterial infections',
                'purchase_price' => 15.00,
                'selling_price' => 30.00,
                'quantity' => 80,
                'reorder_level' => 10,
                'expiry_date' => Carbon::now()->addYear(),
                'batch_number' => 'AX2024001',
                'shelf_number' => 'B2',
                'requires_prescription' => true
            ],
            [
                'name' => 'Ibuprofen',
                'generic_name' => 'Ibuprofen',
                'brand' => 'Advil',
                'category_id' => 1,
                'supplier_id' => 1,
                'description' => 'NSAID for pain and inflammation',
                'purchase_price' => 3.00,
                'selling_price' => 6.00,
                'quantity' => 120,
                'reorder_level' => 15,
                'expiry_date' => Carbon::now()->addMonths(18),
                'batch_number' => 'IB2024001',
                'shelf_number' => 'A3',
                'requires_prescription' => false
            ],
            [
                'name' => 'Cetirizine',
                'generic_name' => 'Cetirizine HCl',
                'brand' => 'Zyrtec',
                'category_id' => 4,
                'supplier_id' => 3,
                'description' => 'Antihistamine for allergies',
                'purchase_price' => 8.00,
                'selling_price' => 15.00,
                'quantity' => 5,
                'reorder_level' => 10,
                'expiry_date' => Carbon::now()->addMonths(6),
                'batch_number' => 'CT2024001',
                'shelf_number' => 'C1',
                'requires_prescription' => false
            ],
            [
                'name' => 'Losartan',
                'generic_name' => 'Losartan Potassium',
                'brand' => 'Cozaar',
                'category_id' => 6,
                'supplier_id' => 2,
                'description' => 'For hypertension',
                'purchase_price' => 12.00,
                'selling_price' => 25.00,
                'quantity' => 45,
                'reorder_level' => 10,
                'expiry_date' => Carbon::now()->addMonths(24),
                'batch_number' => 'LS2024001',
                'shelf_number' => 'D2',
                'requires_prescription' => true
            ],
            [
                'name' => 'Omeprazole',
                'generic_name' => 'Omeprazole',
                'brand' => 'Losec',
                'category_id' => 7,
                'supplier_id' => 3,
                'description' => 'For gastric acid reduction',
                'purchase_price' => 10.00,
                'selling_price' => 20.00,
                'quantity' => 60,
                'reorder_level' => 10,
                'expiry_date' => Carbon::now()->addMonths(12),
                'batch_number' => 'OM2024001',
                'shelf_number' => 'C3',
                'requires_prescription' => true
            ]
        ];
        
        foreach ($medicines as $medicine) {
            $med = Medicine::create($medicine);
            $med->updateStatus();
        }
        
        // Sample sales (for demo)
        if (app()->environment('local')) {
            $this->createSampleSales();
        }
    }
    
    private function createSampleSales()
    {
        $users = User::all();
        $medicines = Medicine::all();
        
        for ($i = 0; $i < 20; $i++) {
            $sale = Sale::create([
                'user_id' => $users->random()->id,
                'discount' => rand(0, 50),
                'tax' => rand(0, 30),
                'payment_method' => ['cash', 'card', 'mobile_payment'][rand(0, 2)],
                'created_at' => Carbon::now()->subDays(rand(0, 30))
            ]);
            
            $itemsCount = rand(1, 5);
            $totalAmount = 0;
            
            for ($j = 0; $j < $itemsCount; $j++) {
                $medicine = $medicines->random();
                $quantity = rand(1, 3);
                $unitPrice = $medicine->selling_price;
                $subtotal = $quantity * $unitPrice;
                $totalAmount += $subtotal;
                
                // Add item
                $sale->items()->create([
                    'medicine_id' => $medicine->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal
                ]);
                
                // Update medicine stock
                $medicine->decrement('quantity', $quantity);
                $medicine->updateStatus();
            }
            
            // Update sale totals
            $sale->total_amount = $totalAmount;
            $sale->grand_total = $totalAmount - $sale->discount + $sale->tax;
            $sale->save();
        }
    }
}