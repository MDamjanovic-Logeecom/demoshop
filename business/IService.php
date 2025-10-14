<?php

//namespace business;

require_once __DIR__ . '/../models/Product.php';


interface IService {
    public function getAll();
    public function getBySKU(string $sku);
    public function deleteBySKU(string $sku): bool;
    public function create(array $formData, $imageFile): bool;
    public function update(array $formData, ?array $imageFile): bool;

}