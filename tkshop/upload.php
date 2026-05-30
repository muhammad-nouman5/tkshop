<?php
require_once 'config/database.php';
include 'includes/functions.php';

// Session already started in db.php, so no need to start again
// Just check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$currentUser = getCurrentUser();
$message = '';
$error = '';

// Create uploads directory if not exists
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filename = time() . '_' . basename($file['name']);
    $target_path = 'uploads/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $message = "File uploaded successfully!";
        
        // Log the upload (vulnerable to SQL injection - intentional)
        $user_id = $_SESSION['user_id'];
        $conn->query("INSERT INTO messages (name, email, subject, message) VALUES ('System', 'system@tkshop.com', 'File Upload', 'User $user_id uploaded: $filename')");
    } else {
        $error = "Upload failed!";
    }
}

// Get list of uploaded files
$uploaded_files = [];
if (is_dir('uploads')) {
    $files = scandir('uploads');
    $uploaded_files = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']);
    });
}

include 'includes/header.php';
?>

<style>
    .upload-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .page-title {
        font-size: 1.8rem;
        color: #1a1a2e;
        margin-bottom: 0.5rem;
    }
    
    .page-subtitle {
        color: #666;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .upload-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }
    
    .upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 2.5rem;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .upload-area:hover {
        border-color: #667eea;
        background: #f1f5f9;
    }
    
    .upload-icon {
        font-size: 3.5rem;
        margin-bottom: 1rem;
    }
    
    .upload-area h3 {
        color: #1a1a2e;
        margin-bottom: 0.5rem;
    }
    
    .upload-area p {
        color: #64748b;
        font-size: 0.85rem;
    }
    
    .file-types {
        display: inline-block;
        background: #e2e8f0;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        color: #475569;
        margin-top: 0.5rem;
    }
    
    .btn-upload {
        background: #667eea;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        margin-top: 1rem;
        width: 100%;
        transition: all 0.3s;
    }
    
    .btn-upload:hover {
        background: #5a67d8;
        transform: translateY(-1px);
    }
    
    .file-list-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .file-list-header {
        background: #1a1a2e;
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .file-list-header span:first-child {
        font-size: 1rem;
    }
    
    .file-list-header span:last-child {
        background: #334155;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.75rem;
    }
    
    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.9rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        transition: background 0.2s;
    }
    
    .file-item:hover {
        background: #f8fafc;
    }
    
    .file-item:last-child {
        border-bottom: none;
    }
    
    .file-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .file-icon {
        width: 36px;
        height: 36px;
        background: #e2e8f0;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    .file-name {
        font-weight: 500;
        color: #1e293b;
    }
    
    .file-date {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.2rem;
    }
    
    .file-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-view {
        background: #667eea;
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    
    .btn-view:hover {
        background: #5a67d8;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #94a3b8;
    }
    
    .empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    /* Alert messages */
    .alert {
        padding: 0.85rem 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .alert-success {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }
    
    .alert-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }
    
    @media (max-width: 640px) {
        .upload-container {
            padding: 1rem;
        }
        
        .upload-card {
            padding: 1.25rem;
        }
        
        .file-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .file-actions {
            width: 100%;
        }
        
        .btn-view {
            flex: 1;
            text-align: center;
        }
    }
</style>

<div class="upload-container">
    <h1 class="page-title">Upload Files</h1>
    <p class="page-subtitle">Upload profile pictures, product images, or documents</p>
    
    <?php if ($message): ?>
        <div class="alert alert-success">
            ✅ <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <!-- Upload Card -->
    <div class="upload-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                <div class="upload-icon">📁</div>
                <h3>Click to upload</h3>
                <p>Choose a file from your computer</p>
                <span class="file-types">Supported formats: JPG, PNG, GIF, PDF, ZIP, PHP</span>
                <input type="file" name="file" id="fileInput" style="display: none;" onchange="this.form.submit()">
            </div>
            <button type="submit" name="upload" class="btn-upload">
                Upload File
            </button>
        </form>
    </div>
    
    <!-- Uploaded Files List -->
    <div class="file-list-card">
        <div class="file-list-header">
            <span>📂 Uploaded Files</span>
            <span><?= count($uploaded_files) ?> files</span>
        </div>
        
        <?php if (empty($uploaded_files)): ?>
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>No files uploaded yet</p>
                <p style="font-size: 0.8rem; margin-top: 0.5rem;">Upload a file using the form above</p>
            </div>
        <?php else: ?>
            <?php foreach ($uploaded_files as $file): 
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $is_image = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                $is_php = in_array(strtolower($ext), ['php', 'phtml']);
                $icon = $is_image ? '🖼️' : ($is_php ? '⚙️' : '📄');
            ?>
                <div class="file-item">
                    <div class="file-info">
                        <div class="file-icon"><?= $icon ?></div>
                        <div>
                            <div class="file-name"><?= htmlspecialchars($file) ?></div>
                            <div class="file-date"><?= date("Y-m-d H:i", filemtime("uploads/$file")) ?></div>
                        </div>
                    </div>
                    <div class="file-actions">
                        <a href="uploads/<?= urlencode($file) ?>" class="btn-view" target="_blank">View</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>