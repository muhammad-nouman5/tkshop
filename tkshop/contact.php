<?php
require_once 'config/database.php';
include 'includes/functions.php';

$currentUser = getCurrentUser();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $subject = $conn->real_escape_string($_POST['subject'] ?? '');
    $message = $conn->real_escape_string($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill in all fields!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } else {
        // Vulnerable SQL query (intentional for learning)
        $sql = "INSERT INTO messages (name, email, subject, message, created_at) 
                VALUES ('$name', '$email', '$subject', '$message', NOW())";
        
        if ($conn->query($sql)) {
            $success = "Thank you for contacting us! We'll get back to you within 24 hours.";
            // Clear POST data
            $_POST = [];
        } else {
            $error = "Something went wrong. Please try again later.";
        }
    }
}

include 'includes/header.php';
?>

<!-- Contact Page Specific CSS -->
<style>
    .contact-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .contact-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .contact-header h1 {
        font-size: 2.5rem;
        color: #333;
        margin-bottom: 1rem;
    }
    
    .contact-header p {
        color: #666;
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 2rem;
    }
    
    /* Contact Info Cards */
    .contact-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 20px;
        color: white;
    }
    
    .contact-info h3 {
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding: 0.75rem;
        background: rgba(255,255,255,0.1);
        border-radius: 12px;
        transition: transform 0.3s;
    }
    
    .info-item:hover {
        transform: translateX(5px);
        background: rgba(255,255,255,0.2);
    }
    
    .info-icon {
        width: 45px;
        height: 45px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
    
    .info-content h4 {
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
        opacity: 0.9;
    }
    
    .info-content p {
        font-size: 1rem;
        font-weight: 500;
    }
    
    /* Business Hours */
    .business-hours {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255,255,255,0.2);
    }
    
    .business-hours h4 {
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .business-hours p {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 0.4rem;
    }
    
    /* Contact Form */
    .contact-form-container {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    .contact-form-container h3 {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .form-subtitle {
        color: #666;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
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
    
    .form-group label .required {
        color: #ff4757;
        margin-left: 0.25rem;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.85rem 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s;
        font-family: inherit;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    .btn-submit {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102,126,234,0.3);
    }
    
    /* Alert Messages */
    .alert {
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .alert-success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .alert-error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    /* Map Section */
    .map-section {
        margin-top: 3rem;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    .map-section iframe {
        width: 100%;
        height: 350px;
        border: none;
    }
    
    /* FAQ Section */
    .faq-section {
        margin-top: 3rem;
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 20px;
    }
    
    .faq-section h3 {
        text-align: center;
        font-size: 1.5rem;
        margin-bottom: 2rem;
        color: #333;
    }
    
    .faq-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    
    .faq-item {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .faq-item h4 {
        color: #667eea;
        margin-bottom: 0.5rem;
    }
    
    .faq-item p {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
        
        .contact-header h1 {
            font-size: 1.8rem;
        }
        
        .faq-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="contact-page">
    <div class="contact-header">
        <h1>📬 Get In Touch</h1>
        <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
    </div>
    
    <div class="contact-grid">
        <!-- Contact Info Cards -->
        <div class="contact-info">
            <h3>📍 Contact Information</h3>
            
            <div class="info-item">
                <div class="info-icon">📍</div>
                <div class="info-content">
                    <h4>Visit Us</h4>
                    <p>Bhakkar Punjab Pakistan</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">📞</div>
                <div class="info-content">
                    <h4>Call Us</h4>
                    <p>+923137752297</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">✉️</div>
                <div class="info-content">
                    <h4>Email Us</h4>
                    <p>nomi.cyber@gmail.com</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">💬</div>
                <div class="info-content">
                    <h4>Live Chat</h4>
                    <p>Time, 9AM - 6PM</p>
                </div>
            </div>
            
            <div class="business-hours">
                <h4>🕐 Business Hours</h4>
                <p>Monday - Sunday: 9:00 AM - 9:00 PM</p>
            </div>
        </div>
        
        <!-- Contact Form -->
        <div class="contact-form-container">
            <h3>✉️ Send Us a Message</h3>
            <p class="form-subtitle">Fill out the form below and we'll get back to you within 24 hours.</p>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    ✅ <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Your Name <span class="required">*</span></label>
                    <input type="text" name="name" placeholder="John Doe" 
                           value="<?= htmlspecialchars($_POST['name'] ?? ($currentUser['full_name'] ?? '')) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" name="email" placeholder="john@example.com" 
                           value="<?= htmlspecialchars($_POST['email'] ?? ($currentUser['email'] ?? '')) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Subject <span class="required">*</span></label>
                    <select name="subject" required>
                        <option value="">Select a subject</option>
                        <option value="General Inquiry" <?= isset($_POST['subject']) && $_POST['subject'] == 'General Inquiry' ? 'selected' : '' ?>>General Inquiry</option>
                        <option value="Order Issue" <?= isset($_POST['subject']) && $_POST['subject'] == 'Order Issue' ? 'selected' : '' ?>>Order Issue</option>
                        <option value="Product Question" <?= isset($_POST['subject']) && $_POST['subject'] == 'Product Question' ? 'selected' : '' ?>>Product Question</option>
                        <option value="Return/Refund" <?= isset($_POST['subject']) && $_POST['subject'] == 'Return/Refund' ? 'selected' : '' ?>>Return/Refund</option>
                        <option value="Partnership" <?= isset($_POST['subject']) && $_POST['subject'] == 'Partnership' ? 'selected' : '' ?>>Partnership</option>
                        <option value="Other" <?= isset($_POST['subject']) && $_POST['subject'] == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Message <span class="required">*</span></label>
                    <textarea name="message" placeholder="Please describe your question or issue in detail..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn-submit">
                    📤 Send Message
                </button>
            </form>
        </div>
    </div>
    
    <!-- Map Section -->
    <div class="map-section">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3168.717543526762!2d-122.0842496846924!3d37.42206597982509!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fba02425dad8f%3A0x6c296c66619367e!2sGoogleplex!5e0!3m2!1sen!2sus!4v1700000000000!5m2!1sen!2sus" 
            allowfullscreen="" 
            loading="lazy">
        </iframe>
    </div>
    
    <!-- FAQ Section -->
    <div class="faq-section">
        <h3>❓ Frequently Asked Questions</h3>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>How long does shipping take?</h4>
                <p>Standard shipping takes 3-5 business days. Express shipping takes 1-2 business days.</p>
            </div>
            <div class="faq-item">
                <h4>What is your return policy?</h4>
                <p>We offer 30-day returns for unused items in original packaging.</p>
            </div>
            <div class="faq-item">
                <h4>Do you ship internationally?</h4>
                <p>Yes, we ship to over 50 countries worldwide. Shipping costs vary by location.</p>
            </div>
            <div class="faq-item">
                <h4>How can I track my order?</h4>
                <p>You'll receive a tracking number via email once your order ships.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>