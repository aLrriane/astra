<?php
$pageTitle = 'Contact';
$metaDescription = 'Get in touch about Astra, a catalog of celestial objects built as a class project.';
$mainClass = 'page';
include __DIR__ . '/includes/header.php';
?>
        <div class="wrap">
            <h1>Contact</h1>
            <p>Spotted a mistake, or think another object deserves a page? Let us know below.</p>
            <div class="contact-layout">
                <div>
                    <form action="#" onsubmit="return false;">
                        <input type="text" placeholder="Your name">
                        <input type="email" placeholder="Your email">
                        <textarea placeholder="Your message"></textarea>
                        <button type="submit" class="btn">Send Message</button>
                    </form>
                </div>
                <div>
                    <p>This form is for demonstration only.</p>
                </div>
            </div>
        </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
