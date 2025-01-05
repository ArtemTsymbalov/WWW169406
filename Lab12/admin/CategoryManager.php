<?php
class CategoryManager {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function pokazKategorie() {
        echo '<div class="categories-list">';
        echo '<h2>Category Management</h2>';
        
        // Add button for creating new category
        echo '<div class="category-actions">';
        echo '<a href="?action=add_category" class="btn btn-primary">Add New Category</a>';
        echo '</div><br>';
        
        // Get main categories (parent_id = 0)
        $query = "SELECT * FROM categories WHERE parent_id = 0 ORDER BY name ASC";
        $result = $this->conn->query($query);
        
        if ($result->num_rows > 0) {
            while ($category = $result->fetch_assoc()) {
                echo '<div class="category-item">';
                echo '<strong>' . htmlspecialchars($category['name']) . '</strong>';
                echo ' <a href="?action=edit_category&id=' . $category['id'] . '">[Edit]</a>';
                echo ' <a href="?action=delete_category&id=' . $category['id'] . '" onclick="return confirm(\'Delete this category?\')">[Delete]</a>';
                
                // Get subcategories
                $subquery = "SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC";
                $stmt = $this->conn->prepare($subquery);
                $stmt->bind_param("i", $category['id']);
                $stmt->execute();
                $subresult = $stmt->get_result();
                
                if ($subresult->num_rows > 0) {
                    echo '<ul class="subcategories">';
                    while ($subcategory = $subresult->fetch_assoc()) {
                        echo '<li>' . htmlspecialchars($subcategory['name']);
                        echo ' <a href="?action=edit_category&id=' . $subcategory['id'] . '">[Edit]</a>';
                        echo ' <a href="?action=delete_category&id=' . $subcategory['id'] . '" onclick="return confirm(\'Delete this subcategory?\')">[Delete]</a>';
                        echo '</li>';
                    }
                    echo '</ul>';
                }
                echo '</div>';
            }
        } else {
            echo '<p>No categories found</p>';
        }
        echo '</div>';
    }
    
    public function dodajKategorie() {
        // Get all categories for parent selection
        $query = "SELECT * FROM categories WHERE parent_id = 0 ORDER BY name ASC LIMIT 100";
        $result = $this->conn->query($query);
        
        echo '<form method="post" action="?action=add_category">
            <h2>Add New Category</h2>
            <label>Name:</label><br>
            <input type="text" name="category_name" required><br><br>
            
            <label>Parent Category (optional):</label><br>
            <select name="parent_id">
                <option value="0">None (Main Category)</option>';
        
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
        }
        
        echo '</select><br><br>
            <label>Status:</label>
            <input type="checkbox" name="status" value="1" checked><br><br>
            <input type="submit" name="submit_category" value="Add Category">
        </form>';
        
        if (isset($_POST['submit_category'])) {
            $name = $this->conn->real_escape_string($_POST['category_name']);
            $parent_id = (int)$_POST['parent_id'];
            $status = isset($_POST['status']) ? 1 : 0;
            
            $query = "INSERT INTO categories (name, parent_id, status) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sii", $name, $parent_id, $status);
            
            if ($stmt->execute()) {
                echo "Category added successfully!";
            } else {
                echo "Error: " . $this->conn->error;
            }
        }
    }
    
    public function usunKategorie($id) {
        // First delete all subcategories
        $query = "DELETE FROM categories WHERE parent_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Then delete the category itself
        $query = "DELETE FROM categories WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo "Category deleted successfully!";
        } else {
            echo "Error: " . $this->conn->error;
        }
    }
    
    public function edytujKategorie($id) {
        $query = "SELECT * FROM categories WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        
        if (!$category) {
            echo "Category not found.";
            return;
        }
        
        // Get all possible parent categories
        $parent_query = "SELECT * FROM categories WHERE parent_id = 0 AND id != ? ORDER BY name ASC";
        $parent_stmt = $this->conn->prepare($parent_query);
        $parent_stmt->bind_param("i", $id);
        $parent_stmt->execute();
        $parent_result = $parent_stmt->get_result();
        
        echo '<form method="post">
            <h2>Edit Category</h2>
            <label>Name:</label><br>
            <input type="text" name="category_name" value="' . htmlspecialchars($category['name']) . '" required><br><br>
            
            <label>Parent Category:</label><br>
            <select name="parent_id">
                <option value="0" ' . ($category['parent_id'] == 0 ? 'selected' : '') . '>None (Main Category)</option>';
        
        while ($row = $parent_result->fetch_assoc()) {
            $selected = ($category['parent_id'] == $row['id']) ? 'selected' : '';
            echo '<option value="' . $row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['name']) . '</option>';
        }
        
        echo '</select><br><br>
            <label>Status:</label>
            <input type="checkbox" name="status" value="1" ' . ($category['status'] ? 'checked' : '') . '><br><br>
            <input type="submit" name="update_category" value="Update Category">
        </form>';
        
        if (isset($_POST['update_category'])) {
            $name = $this->conn->real_escape_string($_POST['category_name']);
            $parent_id = (int)$_POST['parent_id'];
            $status = isset($_POST['status']) ? 1 : 0;
            
            $update_query = "UPDATE categories SET name = ?, parent_id = ?, status = ? WHERE id = ? LIMIT 1";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bind_param("siii", $name, $parent_id, $status, $id);
            
            if ($update_stmt->execute()) {
                echo "Category updated successfully!";
            } else {
                echo "Error: " . $this->conn->error;
            }
        }
    }
}