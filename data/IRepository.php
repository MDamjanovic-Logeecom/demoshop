<?php

interface IRepository {
    public function getAll();
    public function getBySKU(string $sku);
    public function deleteBySKU(string $sku): bool;
    public function create(Product $product): bool;
    public function update(Product $product): bool;
}