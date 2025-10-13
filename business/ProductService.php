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
        return $this->repository->deleteBySKU($sku);
    }

    public function create(Product $product): bool {
        //TODO: image checking and conversion b4 sending to repo

        return $this->repository->create($product);
    }

    public function update(Product $product): bool {
        //TODO: image checking and conversion b4 sending to repo
        return $this->repository->update($product);
    }

    //-----------------------------------------------------------Helper functions:
    //TODO: use this probably for the image validation during upload!
    function imageIsOkay($imageFile) {
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