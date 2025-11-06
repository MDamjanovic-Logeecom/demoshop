<?php
require_once __DIR__ . '/../src/Bootstrap/Bootstrap.php';

use Demoshop\Local\Data\Models\EloquentCategory;
use Demoshop\Local\Data\Models\EloquentProduct;
use Demoshop\Local\Data\Models\EloquentUser;

try {

    $categories = [
        ['title' => 'Electronics', 'parent_id' => null, 'code' => 'ELEC', 'description' => 'All electronic items'],
        ['title' => 'Computers', 'parent_id' => null, 'code' => 'COMP', 'description' => 'Computers and accessories'],
        ['title' => 'Laptops', 'parent_id' => null, 'code' => 'LAPT', 'description' => 'All kinds of laptops'],
        ['title' => 'Smartphones', 'parent_id' => 1, 'code' => 'SMART', 'description' => 'Smartphones and accessories'],
        ['title' => 'Tablets', 'parent_id' => 1, 'code' => 'TAB', 'description' => 'Tablet devices'],
        ['title' => 'Desktops', 'parent_id' => 2, 'code' => 'DESK', 'description' => 'Desktop computers'],
        ['title' => 'Components', 'parent_id' => 2, 'code' => 'COMP-COMP', 'description' => 'Computer components'],
        [
            'title' => 'Gaming Laptops',
            'parent_id' => 3,
            'code' => 'GAM-LAPT',
            'description' => 'High-performance gaming laptops'
        ],
        [
            'title' => 'Work Laptops',
            'parent_id' => 3,
            'code' => 'WORK-LAPT',
            'description' => 'Office and productivity laptops'
        ],
        ['title' => 'Ultrabooks', 'parent_id' => 3, 'code' => 'ULTRA', 'description' => 'Thin and light laptops'],
    ];

    foreach ($categories as $cat) {
        EloquentCategory::updateOrCreate(['code' => $cat['code']], $cat);
    }

    $products = [
        [
            'SKU' => 'ELEC001',
            'Title' => '4K TV',
            'Brand' => 'Samsung',
            'Category' => 'ELEC',
            'Dscrptn' => 'Smart 4K TV',
            'LDscrptn' => 'High-quality 4K UHD television',
            'Price' => 799.99,
            'Enabled' => false
        ],
        [
            'SKU' => 'ELEC002',
            'Title' => 'Bluetooth Speaker',
            'Brand' => 'JBL',
            'Category' => 'ELEC',
            'Dscrptn' => 'Portable speaker',
            'LDscrptn' => 'Wireless Bluetooth speaker with bass boost',
            'Price' => 129.99,
            'Enabled' => true
        ],
        [
            'SKU' => 'COMP001',
            'Title' => 'Desktop PC',
            'Brand' => 'HP',
            'Category' => 'COMP',
            'Dscrptn' => 'All-in-one desktop',
            'LDscrptn' => 'Powerful desktop computer for home or office',
            'Price' => 999.99,
            'Enabled' => true
        ],
        [
            'SKU' => 'COMP002',
            'Title' => 'External Hard Drive',
            'Brand' => 'Seagate',
            'Category' => 'COMP',
            'Dscrptn' => '1TB HDD',
            'LDscrptn' => 'External hard drive with USB 3.0 support',
            'Price' => 89.99,
            'Enabled' => true
        ],
        [
            'SKU' => 'LAPT001',
            'Title' => 'Basic Laptop',
            'Brand' => 'Dell',
            'Category' => 'LAPT',
            'Dscrptn' => 'Entry-level laptop',
            'LDscrptn' => '15-inch laptop with Intel i3 processor',
            'Price' => 549.99,
            'Enabled' => false
        ],
        [
            'SKU' => 'LAPT002',
            'Title' => 'Student Laptop',
            'Brand' => 'Lenovo',
            'Category' => 'LAPT',
            'Dscrptn' => 'Affordable student laptop',
            'LDscrptn' => 'Lightweight and reliable laptop for students',
            'Price' => 499.99,
            'Enabled' => true
        ],
        [
            'SKU' => 'SMART001',
            'Title' => 'Smartphone A',
            'Brand' => 'Apple',
            'Category' => 'SMART',
            'Dscrptn' => 'Latest iPhone',
            'LDscrptn' => 'Flagship smartphone with advanced camera system',
            'Price' => 1099.99,
            'Enabled' => true
        ],
        [
            'SKU' => 'SMART002',
            'Title' => 'Smartphone B',
            'Brand' => 'Samsung',
            'Category' => 'SMART',
            'Dscrptn' => 'Galaxy model',
            'LDscrptn' => 'High-end Android smartphone with AMOLED display',
            'Price' => 899.99,
            'Enabled' => false
        ],
        [
            'SKU' => 'TAB001',
            'Title' => 'Tablet A',
            'Brand' => 'Apple',
            'Category' => 'TAB',
            'Dscrptn' => 'iPad Air',
            'LDscrptn' => 'Light and powerful tablet with A15 chip',
            'Price' => 699.99,
            'Enabled' => false
        ],
        [
            'SKU' => 'TAB002',
            'Title' => 'Tablet B',
            'Brand' => 'Samsung',
            'Category' => 'TAB',
            'Dscrptn' => 'Galaxy Tab S9',
            'LDscrptn' => 'Android tablet for entertainment and productivity',
            'Price' => 649.99,
            'Enabled' => true
        ],
        [
            'SKU' => 'DESK001',
            'Title' => 'Gaming Desktop',
            'Brand' => 'MSI',
            'Category' => 'DESK',
            'Dscrptn' => 'Gaming PC',
            'LDscrptn' => 'High-performance desktop with RTX GPU',
            'Price' => 1499.99,
            'Enabled' => true
        ],
        [
            'SKU' => 'DESK002',
            'Title' => 'Office Desktop',
            'Brand' => 'Dell',
            'Category' => 'DESK',
            'Dscrptn' => 'Business desktop',
            'LDscrptn' => 'Compact and quiet desktop for offices',
            'Price' => 849.99,
            'Enabled' => false
        ],
        [
            'SKU' => 'COMP-COMP001',
            'Title' => 'Graphics Card',
            'Brand' => 'NVIDIA',
            'Category' => 'COMP-COMP',
            'Dscrptn' => 'RTX 4070',
            'LDscrptn' => 'Powerful GPU for gaming and rendering',
            'Price' => 599.99,
            'Enabled' => true
        ],
        [
            'SKU' => 'COMP-COMP002',
            'Title' => 'RAM 16GB',
            'Brand' => 'Corsair',
            'Category' => 'COMP-COMP',
            'Dscrptn' => 'DDR5 RAM kit',
            'LDscrptn' => 'High-speed 16GB DDR5 memory kit',
            'Price' => 149.99,
            'Enabled' => true
        ],
        [
            'SKU' => 'GAM-LAPT001',
            'Title' => 'Predator Gaming Laptop',
            'Brand' => 'Acer',
            'Category' => 'GAM-LAPT',
            'Dscrptn' => 'Gaming beast',
            'LDscrptn' => 'RTX 4060, i7, 16GB RAM',
            'Price' => 1699.99,
            'Enabled' => false
        ],
        [
            'SKU' => 'GAM-LAPT002',
            'Title' => 'ROG Strix',
            'Brand' => 'Asus',
            'Category' => 'GAM-LAPT',
            'Dscrptn' => 'Pro gaming laptop',
            'LDscrptn' => 'Ryzen 9, RTX 4070, 1TB SSD',
            'Price' => 1999.99,
            'Enabled' => true
        ],
        [
            'SKU' => 'WORK-LAPT001',
            'Title' => 'ThinkPad X1',
            'Brand' => 'Lenovo',
            'Category' => 'WORK-LAPT',
            'Dscrptn' => 'Business laptop',
            'LDscrptn' => 'Durable laptop for professionals',
            'Price' => 1399.99,
            'Enabled' => false
        ],
        [
            'SKU' => 'WORK-LAPT002',
            'Title' => 'HP EliteBook',
            'Brand' => 'HP',
            'Category' => 'WORK-LAPT',
            'Dscrptn' => 'Premium work laptop',
            'LDscrptn' => 'Secure and powerful for enterprise use',
            'Price' => 1299.99,
            'Enabled' => false
        ],
        [
            'SKU' => 'ULTRA001',
            'Title' => 'MacBook Air M2',
            'Brand' => 'Apple',
            'Category' => 'ULTRA',
            'Dscrptn' => 'Light ultrabook',
            'LDscrptn' => 'M2 chip, silent, long battery life',
            'Price' => 1199.99,
            'Enabled' => true
        ],
        [
            'SKU' => 'ULTRA002',
            'Title' => 'Dell XPS 13',
            'Brand' => 'Dell',
            'Category' => 'ULTRA',
            'Dscrptn' => 'Premium ultrabook',
            'LDscrptn' => '13-inch ultrabook with OLED display',
            'Price' => 1149.99,
            'Enabled' => true
        ],
    ];

    foreach ($products as $prod) {
        EloquentProduct::updateOrCreate(['SKU' => $prod['SKU']], $prod);
    }

    EloquentUser::updateOrCreate(
        ['username' => 'admin'],
        ['password' => password_hash('admin123', PASSWORD_BCRYPT)]
    );

    echo "Seeding completed successfully.\n";

} catch (\Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
}

