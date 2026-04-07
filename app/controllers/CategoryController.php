<?php
// Require SessionHelper and other necessary files
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');
class CategoryController
{
    private $categoryModel;
    private $db;
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }
    public function list()
    {
        $categories = $this->categoryModel->getCategories();
        include 'app/views/category/list.php';
    }

    public function getAllCategories()
    {
        $categoryModel = new CategoryModel($this->db);
        return $categoryModel->getCategories();
    }

    // Lấy sản phẩm theo danh mục
    public function getProductsByCategory($category_id)
    {
        $productModel = new ProductModel($this->db);
        return $productModel->getProductsByCategory($category_id);
    }
}
