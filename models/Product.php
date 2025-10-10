<?php

//namespace models;

class Product {
    private string $sku;
    private string $title;
    private string $brand;
    private string $category;
    private ?string $shortDescription;
    private ?string $longDescription;
    private float $price;
    private ?string $image;
    private bool $enabled;

    public function __construct(
        string $sku,
        string $title,
        string $brand,
        string $category,
        ?string $shortDescription = null,
        ?string $longDescription = null,
        float $price = 0.0,
        ?string $image = null,
        bool $enabled = true
    ) {
        $this->sku = $sku;
        $this->title = $title;
        $this->brand = $brand;
        $this->category = $category;
        $this->shortDescription = $shortDescription;
        $this->longDescription = $longDescription;
        $this->price = $price;
        $this->image = $image;
        $this->enabled = $enabled;
    }

    // Getters
    public function getSKU(): string {
        return $this->sku;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getBrand(): string {
        return $this->brand;
    }

    public function getCategory(): string {
        return $this->category;
    }

    public function getShortDescription(): ?string {
        return $this->shortDescription;
    }

    public function getLongDescription(): ?string {
        return $this->longDescription;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function isEnabled(): bool {
        return $this->enabled;
    }

    // Setters
    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function setBrand(string $brand): void {
        $this->brand = $brand;
    }

    public function setCategory(string $category): void {
        $this->category = $category;
    }

    public function setShortDescription(?string $desc): void {
        $this->shortDescription = $desc;
    }

    public function setLongDescription(?string $desc): void {
        $this->longDescription = $desc;
    }

    public function setPrice(float $price): void {
        $this->price = $price;
    }

    public function setImage(?string $image): void {
        $this->image = $image;
    }

    public function setEnabled(bool $enabled): void {
        $this->enabled = $enabled;
    }

}