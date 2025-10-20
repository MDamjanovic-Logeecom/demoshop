<?php

namespace Demoshop\Local\Business;

use Demoshop\Local\Data\IProductRepository;
use Demoshop\Local\DTO\ProductDTO;
use Demoshop\Local\Models\Product;

/**
 * Class ProductService
 *
 * Service layer for product operations.
 * Responsible for orchestrating product-related business logic
 * and delegating operations to the ProductRepository.
 * Implements the IProductService interface.
 */
class ProductService implements IProductService
{
    /**
     * @var IProductRepository Repository used to access product data.
     * Concrete instance is injected in the constructor.
     */
    private IProductRepository $repository;

    /**
     * Constants for validating image proportions
     */
    const int MIN_WIDTH = 600;
    const int|float MIN_ASPECT_RATIO = 4 / 3;
    const int|float MAX_ASPECT_RATIO = 16 / 9;

    /**
     * @param IProductRepository $repository Repository instance for database operations.
     */
    public function __construct(IProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieves all products from the repository.
     *
     * @return Product[] Array of Product objects.
     */
    public function getAll(): array
    {
        $products = $this->repository->getAll();
        $productDTOs = [];

        foreach ($products as $product) {
            $dto = $this->modelToDTO($product);
            $productDTOs[] = $dto;
        }

        return $productDTOs;
    }

    /**
     * Retrieves a single product by SKU.
     *
     * @param string $sku The SKU of the product to retrieve.
     *
     * @return Product|null The product object.
     */
    public function getBySKU(string $sku): ?ProductDTO
    {
        $product = $this->repository->getBySKU($sku);

        if ($product) {
            $dto = $this->modelToDTO($product);

            return $dto;
        }

        return null;
    }

    /**
     * Deletes a product by SKU.
     *
     * @param string $sku The SKU of the product to delete.
     *
     * @return bool True if deletion was successful, false otherwise.
     */
    public function deleteBySKU(string $sku): bool
    {
        if (empty($sku)) {

            return false;
        }

        return $this->repository->deleteBySKU($sku);
    }

    /**
     * Creates a new product using the provided form data and optional image file.
     *
     * @param ProductDTO $productDTO Object containing submitted product data (SKU, title, brand, category, etc.).
     *
     * @return bool True if the product was successfully created, false otherwise.
     */
    public function create(ProductDTO $productDTO): bool
    {
        $product = $this->DTOtoModel($productDTO);
        $imageData = $formData['image'] ?? null;

        if ($imageData == null) {
            return $this->repository->create($product);
        }

        if ($this->IsImageOkay($imageData)) {
            return $this->repository->create($product);
        }

        return false;
    }

    /**
     * Updates an existing product using the provided form data and optional image file.
     *
     * @param ProductDTO $productDTO Object of submitted product data (SKU, title, brand, category, etc.).
     *
     * @return bool True if the product was successfully updated, false otherwise.
     */
    public function update(ProductDTO $productDTO): bool
    {
        $product = $this->DTOtoModel($productDTO);
        $imageData = $productDTO->image ?? null;

        if ($imageData == null) {
            return $this->repository->update($product);
        }

        if ($this->IsImageOkay($imageData)) {
            return $this->repository->update($product);
        }

        return false;
    }

    /**
     * Validates an uploaded image file.
     *
     * Checks whether the file was actually uploaded, is a valid image,
     * meets minimum width requirements, and has an acceptable aspect ratio (4:3 to 16:9).
     *
     * @param string|null $imageData
     *
     * @return bool True if the image is valid, false otherwise.
     */
    private function IsImageOkay(?string $imageData): bool
    {
        if (empty($imageData)) {
            return false;
        }

        $info = @getimagesizefromstring($imageData);

        if ($info === false) {
            return false;
        }

        $width = $info[0];
        $height = $info[1];
        $ratio = $width / $height;

        if ($width < self::MIN_WIDTH) {
            return false;
        }

        // Aspect ratio (4:3 - 16:9)
        if ($ratio < self::MIN_ASPECT_RATIO || $ratio > self::MAX_ASPECT_RATIO) {
            return false;
        }

        return true;
    }

    /**
     * Converts product DTO to model object.
     *
     * @param ProductDTO $dto Object that contains information from form.
     *
     * @return Product dto converted to the project model object
     */
    private function DTOtoModel(ProductDTO $dto): Product
    {
        $product = new Product(
            sku: $dto->sku,
            title: $dto->title,
            brand: $dto->brand,
            category: $dto->category,
            shortDescription: $dto->shortDescription,
            longDescription: $dto->description,
            price: $dto->price,
            image: $dto->image,
            enabled: $dto->enabled,
        );

        return $product;
    }

    /**
     * Converts product model to DTO object.
     *
     * @param Product $product
     *
     * @return ProductDTO model converted to DTO for presentation layer
     */
    private function modelToDTO(Product $product): ProductDTO
    {
        return new ProductDTO(
            sku: $product->getSku(),
            title: $product->getTitle(),
            brand: $product->getBrand(),
            category: $product->getCategory(),
            shortDescription: $product->getShortDescription(),
            description: $product->getLongDescription(),
            enabled: $product->isEnabled(),
            price: $product->getPrice(),
            image: $product->getImage(),
        );
    }
}