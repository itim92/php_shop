<?php

use App\Service\VendorService;

require_once __DIR__ . '/../../App/bootstrap.php';

$faker = Faker\Factory::create();
$company = new Faker\Provider\en_US\Company($faker);

for ($i = 0; $i < 10; $i++) {
    $vendor = new \App\Model\Vendor();
    $vendor->setName($company->company());

    echo $vendor->getName() . "\n";

    VendorService::save($vendor);
}
