<?php

//namespace business;
require_once __DIR__ . '/IService.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../data/ProductRepository.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ProductService implements IService {
    private ProductRepository $repository;

    public function __construct(ProductRepository $repository) {
        $this->repository = $repository;
    }

    //----------------------------------------------------------Repository functions:
    public function getAll() : array{
        return $this->repository->getAll();
    }

    public function getBySKU(string $sku): Product {
        return $this->repository->getBySKU($sku);
    }

    public function deleteBySKU(string $sku): bool {
        if (empty($sku)) {
            return false;
        }
        return $this->repository->deleteBySKU($sku);
    }

    public function create(array $formData, $imageFile): bool {
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Capture submitted data
            $sku = isset($_POST['sku']) ? $_POST['sku'] : ''; // isset: if the field exists, use the value; if not -> use ''
            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $brand = isset($_POST['brand']) ? $_POST['brand'] : '';
            $category = isset($_POST['category']) ? $_POST['category'] : '';
            $sdescription = isset($_POST['short_description']) ? $_POST['short_description'] : '';
            $ldescription = isset($_POST['description']) ? $_POST['description'] : '';
            $enabled = isset($_POST['enabled']) ? 1 : 0;
            $featured = isset($_POST['featured']) ? 1 : 0;
            $imageFile = isset($_FILES['image']) ? $_FILES['image'] : null; // Capture uploaded image file if any
            $price = (float)(isset($_POST['price']) ? $_POST['price'] : 0.0);

            if ($sku == null || $title == null){
                echo "<script>
                    alert('SKU and Title are required.');
                    window.location.href = 'index.php?page=list';
                  </script>";
            }

            if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) { // If img uploaded -> check if valid b4 sending to db
                if($this->imageIsOkay($imageFile)) {

                    $imageData = file_get_contents($imageFile['tmp_name']);

                    $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price, $imageData, $enabled);
                    return $this->repository->create($product);

                }
            }else { // If no img upload, nothing to validate
                $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price, null, $enabled);
                return $this->repository->create($product);
            }
        }
        return false;
    }

    public function update(array $formData, ?array $imageFile): bool {
        //Capture submitted data
        $sku = isset($_POST['sku']) ? $_POST['sku'] : ''; // isset: if the field exists, use the value; if not -> use ''
        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $brand = isset($_POST['brand']) ? $_POST['brand'] : '';
        $category = isset($_POST['category']) ? $_POST['category'] : '';
        $sdescription = isset($_POST['short_description']) ? $_POST['short_description'] : '';
        $ldescription = isset($_POST['description']) ? $_POST['description'] : '';
        $enabled = isset($_POST['enabled']) ? 1 : 0;
        $featured = isset($_POST['featured']) ? 1 : 0;
        $imageFile = isset($_FILES['image']) ? $_FILES['image'] : null; // Capture uploaded image file if any
        $price = isset($_POST['price']) ? $_POST['price'] : 0;

        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) { // If img uploaded -> check if valid b4 sending to db
            if($this->imageIsOkay($imageFile)) {

                $imageData = file_get_contents($imageFile['tmp_name']);

                $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price, $imageData, $enabled);
                return $this->repository->update($product);
            }
        }else { // If no img upload, nothing to validate
            $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price, null, $enabled);
            return $this->repository->update($product);
        }
        return false;
    }

    //-----------------------------------------------------------Helper functions:

    function imageIsOkay($imageFile): bool {
        // Ensure a file was actually uploaded
        if (!isset($imageFile['tmp_name']) || !is_uploaded_file($imageFile['tmp_name'])) {
            echo "<script>alert('No valid image file uploaded.');</script>";
            return false;
        }

        // Get image dimensions and validate
        $info = @getimagesize($imageFile['tmp_name']);
        if ($info === false) {
            echo "<script>
            alert('Uploaded file is not a valid image.');
            window.location.href = 'index.php?page=list';
            </script>";
            return false;
        }

        $width = $info[0];
        $height = $info[1];
        $ratio = $width / $height;

        // Minimum width check
        if ($width < 600) {
            echo "<script>
            alert('Image width must be at least 600px.');
            window.location.href = 'index.php?page=list';
            </script>";
            return false;
        }

        // Aspect ratio check (4:3 - 16:9)
        if ($ratio < (4/3) || $ratio > (16/9)) {
            echo "<script>
            alert('Image aspect ratio must be between 4:3 and 16:9.');
            window.location.href = 'index.php?page=list';
            </script>";
            return false;
        }

        return true;
    }
}