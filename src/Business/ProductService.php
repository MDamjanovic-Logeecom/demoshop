<?php

namespace Demoshop\Local\Business;

use Demoshop\Local\Data\IProductRepository;
use Demoshop\Local\Infrastructure\http\HttpRequest;
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
     * @param IProductRepository $repository Repository instance for database operations.
     */
    public function __construct(IProductRepository $repository)
    {
        $this->repository = $repository;
    }

    //----------------------------------------------------------Repository functions:
    /**
     * Retrieves all products from the repository.
     *
     * @return Product[] Array of Product objects.
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
     * @return Product|null The product object.
     */
    public function getBySKU(string $sku): ?Product
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
     * @param array $formData Associative array of submitted product data (e.g., SKU, title, brand, etc.).
     * @param array|null $imageFile Uploaded image file information from $_FILES (if any).
     *
     * @return bool True if the product was successfully created, false otherwise.
     */
    public function create(array $formData, mixed $imageFile): bool
    {
        $wrapper = new HttpRequest();

        // Handle form submission
        if ($wrapper->getServer('REQUEST_METHOD') === 'POST') {
            // Capture submitted data
            $sku = $wrapper->getHttpPost('sku', ''); // isset: if the field exists, use the value; if not -> use ''
            $title = $wrapper->getHttpPost('title', '');
            $brand = $wrapper->getHttpPost('brand', '');
            $category = $wrapper->getHttpPost('category', '');
            $sdescription = $wrapper->getHttpPost('short_description', '');
            $ldescription = $wrapper->getHttpPost('description', '');
            $enabled = $wrapper->getHttpPost('enabled', 0) ? 1 : 0;
            $imageFile = $wrapper->getFiles('image', null); // Capture uploaded image file if any
            $price = (float)$wrapper->getHttpPost('price', 0.0);

            if ($sku == null || $title == null) {
                return false;
            }

            if (!$imageFile || $imageFile['error'] !== UPLOAD_ERR_OK) { // If image not uploaded -> send to db with null image
                $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price, null,
                    $enabled);

                return $this->repository->create($product);
            }

            // If img uploaded -> check if valid before sending to db
            if ($this->imageIsOkay($imageFile)) {
                $imageData = file_get_contents($imageFile['tmp_name']);

                $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price,
                    $imageData, $enabled);

                return $this->repository->create($product);
            }
        }

        return false;
    }

    /**
     * Updates an existing product using the provided form data and optional image file.
     *
     * @param array $formData Associative array of submitted product data (SKU, title, brand, category, etc.).
     * @param array|null $imageFile Uploaded image file information from $_FILES (if any).
     *
     * @return bool True if the product was successfully updated, false otherwise.
     */
    public function update(array $formData, ?array $imageFile): bool
    {
        $wrapper = new HttpRequest();

        //Capture submitted data
        $sku = $wrapper->getHttpPost('sku', ''); // isset: if the field exists, use the value; if not -> use ''
        $title = $wrapper->getHttpPost('title', '');
        $brand = $wrapper->getHttpPost('brand', '');
        $category = $wrapper->getHttpPost('category', '');
        $sdescription = $wrapper->getHttpPost('short_description', '');
        $ldescription = $wrapper->getHttpPost('description', '');
        $enabled = $wrapper->getHttpPost('enabled', 0) ? 1 : 0;
        $imageFile = $wrapper->getFiles('image', null); // Capture uploaded image file if any
        $price = (float)$wrapper->getHttpPost('price', 0.0);

        if (!$imageFile || $imageFile['error'] !== UPLOAD_ERR_OK) { // If image not uploaded -> send to db with null image
            $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price, null,
                $enabled);

            return $this->repository->update($product);
        }

        // If img uploaded -> check if valid b4 sending to db
        if ($this->imageIsOkay($imageFile)) {
            $imageData = file_get_contents($imageFile['tmp_name']);

            $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price,
                $imageData, $enabled);

            return $this->repository->update($product);
        }

        return false;
    }

    //-----------------------------------------------------------Helper functions:

    /**
     * Validates an uploaded image file.
     *
     * Checks whether the file was actually uploaded, is a valid image,
     * meets minimum width requirements, and has an acceptable aspect ratio (4:3 to 16:9).
     * @param array $imageFile Uploaded file information from $_FILES.
     *
     * @return bool True if the image is valid, false otherwise.
     */
    function imageIsOkay($imageFile): bool
    {
        // Ensures a file was actually uploaded
        if (!isset($imageFile['tmp_name']) || !is_uploaded_file($imageFile['tmp_name'])) {
            return false;
        }

        // Get image dimensions and validate
        $info = @getimagesize($imageFile['tmp_name']);
        if ($info === false) {
            return false;
        }

        $minwidth = 600;
        $minAspectRatio = 4 / 3;
        $maxAspectRatio = 16 / 9;

        $width = $info[0];
        $height = $info[1];
        $ratio = $width / $height;

        // Minimum width check
        if ($width < $minwidth) {
            return false;
        }

        // Aspect ratio check (4:3 - 16:9)
        if ($ratio < $minAspectRatio || $ratio > $maxAspectRatio) {
            return false;
        }

        return true;
    }
}