<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    
<style>
  /* Wrapper to push footer to bottom if page is short */
  .page-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }
  .content-area {
    flex: 1;
  }

  /* --- MODERNIZED FOOTER STYLES --- */
  .dose-footer {
    background-color: #1a2d42; /* Deep Navy Blue */
    color: #e2e8f0;
    padding-top: 40px; /* More breathing room */
    padding-bottom: 20px;
    text-align: center;
    width: 100%;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    border-top: 4px solid #565e8b; /* Accent border on top */
    position: relative;
    z-index: 10;
  }

  .footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
  }

  /* Typography */
  .footer-logo {
    display: inline-block;
    font-size: 1.75rem !important;
    font-weight: 700 !important;
    color: #ffffff;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
  }

  .dose-footer p {
    color: #a0aec0; /* Softer text color for description */
    font-size: 0.95rem;
    margin-bottom: 20px;
  }

  /* Links Styling */
  .footer-links {
    list-style: none;
    padding: 0;
    margin: 20px 0; 
    display: flex;
    justify-content: center;
    flex-wrap: wrap; /* Allows wrapping on small screens */
    gap: 25px; /* Spacing between links */
  }

  .footer-links li a {
    color: #cbd5e0;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease; /* Smooth transition */
    padding-bottom: 2px;
    border-bottom: 1px solid transparent;
  }

  /* Hover Effect */
  .footer-links li a:hover {
    color: #ffffff;
    border-bottom: 1px solid #63b3ed; /* Light blue underline on hover */
    transform: translateY(-2px);
  }

  /* Copyright Section */
  .copyright {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1); /* Thin separator line */
    font-size: 0.8rem;
    color: #718096;
  }
</style>

<div class="page-wrapper">
  <div class="content-area"></div>

  <footer class="dose-footer">
    <div class="footer-content">
      <span class="footer-logo">DoseCare</span>
      <p>Empowering medication adherence through smart technology.</p>
      
      <ul class="footer-links">
        <li><a href="#">Privacy Policy</a></li>
        <li><a href="#">Terms of Service</a></li>
        <li><a href="#">Contact Support</a></li>
      </ul>

      <div class="copyright">
        &copy; <span id="current-year"></span> DoseCare Adherence Systems. All rights reserved.
      </div>
    </div>

    <script>
      document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
  </footer>
</div>