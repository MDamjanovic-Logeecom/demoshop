<?php

namespace Demoshop\Local\Business;

use Demoshop\Local\Data\IProductRepository;
use Demoshop\Local\DTO\ProductDTO;

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
     * @return ProductDTO[] Array of Product objects.
     */
    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    /**
     * Retrieves a single product by SKU.
     *
     * @param string $sku The SKU of the product to retrieve.
     *
     * @return ProductDTO|null The product object.
     */
    public function getBySKU(string $sku): ?ProductDTO
    {
        return $this->repository->getBySKU($sku);
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
     * @return ProductDTO|null DTO if the product was successfully created, null otherwise.
     */
    public function create(ProductDTO $productDTO): ?ProductDTO
    {
        $imageData = $formData['image'] ?? null;

        $returnDTO = null;

        if ($imageData == null) {
            $returnDTO = $this->repository->create($productDTO);
        }

        if ($this->IsImageOkay($imageData)) {
            $returnDTO = $this->repository->create($productDTO);
        }

        return $returnDTO;
    }

    /**
     * Updates an existing product using the provided form data and optional image file.
     *
     * @param ProductDTO $productDTO Object of submitted product data (SKU, title, brand, category, etc.).
     *
     * @return ProductDTO|null DTO if the product was successfully updated, null otherwise.
     */
    public function update(ProductDTO $productDTO): ?ProductDTO
    {
        $imageData = $productDTO->image ?? null;

        $returnDTO = null;

        if ($imageData == null) {
            $returnDTO = $this->repository->update($productDTO);
        }

        if ($this->IsImageOkay($imageData)) {
            $returnDTO = $this->repository->update($productDTO);
        }

        return $returnDTO;
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
}
