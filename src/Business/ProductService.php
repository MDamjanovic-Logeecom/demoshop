<?php

/**
 * Class ProductService
 *
 * Service layer for product operations.
 * Responsible for orchestrating product-related business logic
 * and delegating operations to the ProductRepository.
 * Implements the IService interface.
 */
class ProductService implements IService
{
    /**
     * @var ProductRepository Repository used to access product data.
     */
    private ProductRepository $repository;

    /**
     * @param ProductRepository $repository Repository instance for database operations.
     */
    public function __construct(ProductRepository $repository)
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
     * @return Product The product object.
     */
    public function getBySKU(string $sku): Product
    {
        return $this->repository->getBySKU($sku);
    }

    /**
     * Deletes a product by SKU.
     *
     * @param string $sku The SKU of the product to delete.
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
     * @return bool True if the product was successfully created, false otherwise.
     */
    public function create(array $formData, mixed $imageFile): bool
    {
        $wrapper = new Wrapper();

        // Handle form submission
        if ($wrapper->getServer('REQUEST_METHOD') === 'POST') {
            // Capture submitted data
            $sku = $wrapper->getPost('sku', ''); // isset: if the field exists, use the value; if not -> use ''
            $title = $wrapper->getPost('title', '');
            $brand = $wrapper->getPost('brand', '');
            $category = $wrapper->getPost('category', '');
            $sdescription = $wrapper->getPost('short_description', '');
            $ldescription = $wrapper->getPost('description', '');
            $enabled = $wrapper->getPost('enabled', 0) ? 1 : 0;
            $imageFile = $wrapper->getFiles('image', null); // Capture uploaded image file if any
            $price = (float)$wrapper->getPost('price', 0.0);

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
     * @return bool True if the product was successfully updated, false otherwise.
     */
    public function update(array $formData, ?array $imageFile): bool
    {
        $wrapper = new Wrapper();

        //Capture submitted data
        $sku = $wrapper->getPost('sku', ''); // isset: if the field exists, use the value; if not -> use ''
        $title = $wrapper->getPost('title', '');
        $brand = $wrapper->getPost('brand', '');
        $category = $wrapper->getPost('category', '');
        $sdescription = $wrapper->getPost('short_description', '');
        $ldescription = $wrapper->getPost('description', '');
        $enabled = $wrapper->getPost('enabled', 0) ? 1 : 0;
        $imageFile = $wrapper->getFiles('image', null); // Capture uploaded image file if any
        $price = (float)$wrapper->getPost('price', 0.0);

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

        $width = $info[0];
        $height = $info[1];
        $ratio = $width / $height;

        // Minimum width check
        if ($width < 600) {
            return false;
        }

        // Aspect ratio check (4:3 - 16:9)
        if ($ratio < (4 / 3) || $ratio > (16 / 9)) {
            return false;
        }

        return true;
    }
}