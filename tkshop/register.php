<?php
require_once 'config/database.php';

$error = '';
$success = '';
$upload_error = '';

// Create uploads directory if not exists
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}
if (!is_dir('uploads/profiles')) {
    mkdir('uploads/profiles', 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    $profile_image = '';
    
    // Handle file upload - RCE VULNERABLE! (No validation)
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $filename = time() . '_' . basename($_FILES['profile_image']['name']);
        $target = 'uploads/profiles/' . $filename;
        
        // NO FILE TYPE VALIDATION - ANY FILE CAN BE UPLOADED!
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
            $profile_image = $target;
        } else {
            $upload_error = "Image upload failed!";
        }
    }
    
    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } elseif (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields!";
    } else {
        // MD5 hash (vulnerable)
        $hash = md5($password);
        
        // SQL Injection vulnerable!
        $sql = "INSERT INTO users (username, email, password, full_name, address, phone, profile_image) 
                VALUES ('$username', '$email', '$hash', '$full_name', '$address', '$phone', '$profile_image')";
        
        if ($conn->query($sql)) {
            $success = "Account created successfully! <a href='login.php'>Login here</a>";
        } else {
            $error = "Registration failed: " . $conn->error;
        }
    }
}

include 'includes/header.php';
?>

<style>
    .register-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .register-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .register-card h2 {
        text-align: center;
        color: #1a1a2e;
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1.25rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #333;
    }
    
    .form-group input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.3s;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: #667eea;
    }
    
    /* Image Upload Area */
    .image-upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .image-upload-area:hover {
        border-color: #667eea;
        background: #f1f5f9;
    }
    
    .image-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 1rem;
        display: none;
        border: 3px solid #667eea;
    }
    
    .upload-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .image-upload-area p {
        color: #64748b;
        font-size: 0.85rem;
    }
    
    .btn-register {
        width: 100%;
        background: #667eea;
        color: white;
        border: none;
        padding: 0.85rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        margin-top: 1rem;
    }
    
    .btn-register:hover {
        background: #5a67d8;
    }
    
    .alert {
        padding: 0.85rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        color: #64748b;
    }
    
    .login-link a {
        color: #667eea;
        text-decoration: none;
    }
    
    .required {
        color: #ef4444;
    }
</style>

<div class="register-container">
    <div class="register-card">
        <h2>Create Account</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($upload_error): ?>
            <div class="alert alert-error">⚠️ <?= $upload_error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <!-- Profile Image Upload (RCE Vulnerable) -->
            <div class="form-group">
                <label>Profile Picture <span class="required"></span></label>
                <div class="image-upload-area" onclick="document.getElementById('profile_image').click()">
                    <img id="preview" class="image-preview" alt="Preview">
                    <div class="upload-icon">📸</div>
                    <p>Click to upload profile picture</p>
                    <p style="font-size:0.7rem; color:#94a3b8;">JPG, PNG, GIF, or PHP files allowed</p>
                </div>
                <input type="file" name="profile_image" id="profile_image" accept="image/*,.php" style="display:none" onchange="previewImage(this)">
            </div>
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="John Doe" value="<?= $_POST['full_name'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label>Username <span class="required">*</span></label>
                <input type="text" name="username" placeholder="johndoe" required value="<?= $_POST['username'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input type="email" name="email" placeholder="john@example.com" required value="<?= $_POST['email'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="+1 234 567 8900" value="<?= $_POST['phone'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" placeholder="123 Main St, City" value="<?= $_POST['address'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label>Password <span class="required">*</span></label>
                <input type="password" name="password" placeholder="••••••••" required>
                <small style="color:#64748b;"></small>
            </div>
            
            <div class="form-group">
                <label>Confirm Password <span class="required">*</span></label>
                <input type="password" name="confirm" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn-register">Create Account</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    var preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.style.display = 'block';
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/footer.php'; ?>