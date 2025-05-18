<?php
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-6">
            <h1 class="mb-4" style="color: var(--dark-pink);">Contact Us</h1>
            <p class="lead">We'd love to hear from you! Send us a message and we'll respond as soon as possible.</p>
            
            <div class="mt-5">
                <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2" style="color: var(--primary-color);"></i> Our Address</h5>
                <p>123 Fashion Street, Pink City, India - 123456</p>
                
                <h5 class="mb-3 mt-4"><i class="fas fa-phone me-2" style="color: var(--primary-color);"></i> Call Us</h5>
                <p>+91 9876543210</p>
                
                <h5 class="mb-3 mt-4"><i class="fas fa-envelope me-2" style="color: var(--primary-color);"></i> Email Us</h5>
                <p>info@deshika.com</p>
                
                <h5 class="mb-3 mt-4"><i class="fas fa-clock me-2" style="color: var(--primary-color);"></i> Working Hours</h5>
                <p>Monday - Saturday: 10:00 AM - 8:00 PM</p>
                <p>Sunday: 12:00 PM - 6:00 PM</p>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-pink">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Google Map -->
<div class="container-fluid px-0">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3502.332181222054!2d77.22738831508269!3d28.62894438242378!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cfd5b347eb62d%3A0x52c2b7494e204dce!2sNew%20Delhi%2C%20Delhi!5e0!3m2!1sen!2sin!4v1626781234567!5m2!1sen!2sin" 
            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
</div>

<?php
include 'includes/footer.php';
?>