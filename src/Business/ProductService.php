<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ProductService implements IService
{
    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    //----------------------------------------------------------Repository functions:
    public function getAll(): array
    {

        return $this->repository->getAll();
    }

    public function getBySKU(string $sku): Product
    {

        return $this->repository->getBySKU($sku);
    }

    public function deleteBySKU(string $sku): bool
    {
        if (empty($sku)) {

            return false;
        }

        return $this->repository->deleteBySKU($sku);
    }

    public function create(array $formData, $imageFile): bool
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
            $featured = $wrapper->getPost('featured', 0) ? 1 : 0;
            $imageFile = $wrapper->getFiles('image', null); // Capture uploaded image file if any
            $price = (float)$wrapper->getPost('price', 0.0);

            if ($sku == null || $title == null) {

                return false;
            }

            if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) { // If img uploaded -> check if valid b4 sending to db
                if ($this->imageIsOkay($imageFile)) {

                    $imageData = file_get_contents($imageFile['tmp_name']);

                    $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price,
                        $imageData, $enabled);

                    return $this->repository->create($product);
                }
            } else { // If no img upload, nothing to validate
                $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price, null,
                    $enabled);

                return $this->repository->create($product);
            }
        }

        return false;
    }

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
        $featured = $wrapper->getPost('featured', 0) ? 1 : 0;
        $imageFile = $wrapper->getFiles('image', null); // Capture uploaded image file if any
        $price = (float)$wrapper->getPost('price', 0.0);

        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) { // If img uploaded -> check if valid b4 sending to db
            if ($this->imageIsOkay($imageFile)) {

                $imageData = file_get_contents($imageFile['tmp_name']);

                $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price,
                    $imageData, $enabled);

                return $this->repository->update($product);
            }
        } else { // If no img upload, nothing to validate
            $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price, null,
                $enabled);

            return $this->repository->update($product);
        }

        return false;
    }

    //-----------------------------------------------------------Helper functions:

    function imageIsOkay($imageFile): bool
    {
        // Ensure a file was actually uploaded
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