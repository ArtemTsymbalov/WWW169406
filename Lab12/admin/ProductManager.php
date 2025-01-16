<?php
class ProductManager {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function pokazProdukty() {
        echo '<div class="products-list">';
        echo '<h2>Product Management</h2>';
        
        echo '<div class="product-actions">';
        echo '<a href="?action=add_product" class="btn btn-primary">Add New Product</a>';
        echo '</div><br>';
        
        $query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 ORDER BY p.created_at DESC";
        $result = $this->conn->query($query);
        
        if ($result->num_rows > 0) {
            echo '<table border="1">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>';
            
            while ($product = $result->fetch_assoc()) {
                $availability = $this->checkAvailability($product);
                echo '<tr>
                    <td>' . $product['id'] . '</td>
                    <td>' . htmlspecialchars($product['title']) . '</td>
                    <td>' . htmlspecialchars($product['category_name']) . '</td>
                    <td>' . number_format($product['net_price'], 2) . ' PLN</td>
                    <td>' . $product['stock_quantity'] . '</td>
                    <td>' . $availability . '</td>
                    <td>
                        <a href="?action=edit_product&id=' . $product['id'] . '">[Edit]</a>
                        <a href="?action=delete_product&id=' . $product['id'] . '" 
                           onclick="return confirm(\'Delete this product?\')">[Delete]</a>
                    </td>
                </tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No products found</p>';
        }
        echo '</div>';
    }
    
    private function checkAvailability($product) {
        // Check expiry date
        if (!empty($product['expiry_date']) && strtotime($product['expiry_date']) < time()) {
            return '<span style="color: red;">Expired</span>';
        }
        
        // Check stock quantity
        if ($product['stock_quantity'] <= 0) {
            return '<span style="color: red;">Out of Stock</span>';
        }
        
        // Check availability status
        if (!$product['availability_status']) {
            return '<span style="color: orange;">Unavailable</span>';
        }
        
        return '<span style="color: green;">Available</span>';
    }
    
    public function dodajProdukt() {
        // Get categories for selection
        $query = "SELECT * FROM categories ORDER BY name ASC";
        $categories = $this->conn->query($query);
        
        echo '<form method="post" action="?action=add_product" enctype="multipart/form-data">
            <h2>Add New Product</h2>
            
            <label>Title:</label><br>
            <input type="text" name="title" required><br><br>
            
            <label>Description:</label><br>
            <textarea name="description" rows="5" required></textarea><br><br>
            
            <label>Category:</label><br>
            <select name="category_id" required>
                <option value="">Select Category</option>';
        
        while ($category = $categories->fetch_assoc()) {
            echo '<option value="' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</option>';
        }
        
        echo '</select><br><br>
            
            <label>Net Price:</label><br>
            <input type="number" name="net_price" step="0.01" required><br><br>
            
            <label>VAT Rate (%):</label><br>
            <input type="number" name="vat_rate" step="0.01" required><br><br>
            
            <label>Stock Quantity:</label><br>
            <input type="number" name="stock_quantity" required><br><br>
            
            <label>Dimensions (LxWxH cm):</label><br>
            <input type="text" name="dimensions"><br><br>
            
            <label>Expiry Date:</label><br>
            <input type="date" name="expiry_date"><br><br>
            
            <label>Product Image:</label><br>
            <input type="file" name="product_image"><br><br>
            
            <label>Availability:</label>
            <input type="checkbox" name="availability_status" value="1" checked><br><br>
            
            <input type="submit" name="submit_product" value="Add Product">
        </form>';
        
        if (isset($_POST['submit_product'])) {
            $this->handleProductSubmission();
        }
    }
    
    private function handleProductSubmission() {
        $title = $this->conn->real_escape_string($_POST['title']);
        $description = $this->conn->real_escape_string($_POST['description']);
        $category_id = (int)$_POST['category_id'];
        $net_price = (float)$_POST['net_price'];
        $vat_rate = (float)$_POST['vat_rate'];
        $stock_quantity = (int)$_POST['stock_quantity'];
        $dimensions = $this->conn->real_escape_string($_POST['dimensions']);
        $expiry_date = $_POST['expiry_date'] ? "'" . $_POST['expiry_date'] . "'" : "NULL";
        $availability_status = isset($_POST['availability_status']) ? 1 : 0;
        
        // Handle image upload
        $image_url = '';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $target_dir = "../uploads/products/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $image_url = $target_dir . time() . '_' . basename($_FILES['product_image']['name']);
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $image_url)) {
                $image_url = str_replace('../', '', $image_url);
            }
        }
        
        $query = "INSERT INTO products (title, description, category_id, net_price, vat_rate, 
                                      stock_quantity, dimensions, expiry_date, image_url, 
                                      availability_status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, $expiry_date, ?, ?)";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssiiiissi", $title, $description, $category_id, $net_price, 
                         $vat_rate, $stock_quantity, $dimensions, $image_url, $availability_status);
        
        if ($stmt->execute()) {
            echo "<p>Product added successfully!</p>";
            header("Location: admin.php?action=products");
        } else {
            echo "<p>Error: " . $this->conn->error . "</p>";
        }
    }
    
    public function usunProdukt($productId) {
        // Prepare the delete query
        $query = "DELETE FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("i", $productId);
            if ($stmt->execute()) {
                echo "Product deleted successfully!";
            } else {
                echo "Error deleting product: " . $this->conn->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $this->conn->error;
        }
    }
} 
