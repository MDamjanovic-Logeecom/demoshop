<?php

namespace Demoshop\Local\Models;

/**
 * Class Product
 *
 * Represents a product from the database.
 */
class Product
{
    /** @var string The product's unique SKU */
    private string $sku;

    /** @var string The product title */
    private string $title;

    /** @var string|null The brand name (optional) */
    private ?string $brand;

    /** @var string|null The category of the product */
    private ?string $category;

    /** @var string|null Short description of the product (optional) */
    private ?string $shortDescription;

    /** @var string|null Long description of the product (optional) */
    private ?string $longDescription;

    /** @var float|null Product price */
    private ?float $price;

    /** @var string|null Base64 encoded image or image path (optional) */
    private ?string $image;

    /** @var bool Whether the product is enabled/active */
    private bool $enabled;

    /** @var bool Whether the product is featured */
    private bool $featured;

    /**
     * Product constructor.
     *
     * @param string $sku Unique product identifier
     * @param string $title Product title
     * @param string $brand Brand name (optional)
     * @param string $category Product category
     * @param string|null $shortDescription Short description (optional)
     * @param string|null $longDescription Long description (optional)
     * @param float $price Product price (default 0.0)
     * @param string|null $image Base64 or file path of the product image (optional)
     * @param bool $enabled Whether the product is enabled (default true)
     */
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

        $this->featured = false;
    }

    /**
     * Get the product SKU.
     *
     * @return string
     */
    public function getSKU(): string
    {
        return $this->sku;
    }

    /**
     * Get the product title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the product brand.
     *
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * Get the product category.
     *
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Get the short description of the product.
     *
     * @return string|null
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * Get the long description of the product.
     *
     * @return string|null
     */
    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    /**
     * Get the product price.
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Get the product image (Base64 or path).
     *
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Check if the product is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Check if the product is featured.
     *
     * @return bool
     */
    public function isFeatured(): bool
    {
        return $this->featured;
    }

    /**
     * Set the product title.
     *
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Set the product brand.
     *
     * @param string $brand
     */
    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    /**
     * Set the product category.
     *
     * @param string $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * Set the product short description.
     *
     * @param string|null $desc
     */
    public function setShortDescription(?string $desc): void
    {
        $this->shortDescription = $desc;
    }

    /**
     * Set the product long description.
     *
     * @param string|null $desc
     */
    public function setLongDescription(?string $desc): void
    {
        $this->longDescription = $desc;
    }

    /**
     * Set the product price.
     *
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * Set the product image (Base64 or path).
     *
     * @param string|null $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * Enable or disable the product.
     *
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Mark the product as featured or not.
     *
     * @param bool $featured
     */
    public function setFeatured(bool $featured): void
    {
        $this->featured = $featured;
    }

}