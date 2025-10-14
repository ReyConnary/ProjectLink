<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us - GoodBooks</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: blueviolet;">
    <div class="container">
      <a class="navbar-brand" href="#" style="margin-right: 50px;">
        <img src="img/book-icon-134.png" width="50" height="50" alt="Book Icon" >
        <span style="font-weight: bold; font-size: 1 rem; color: white;">GoodBooks</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="GoodBooks.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="Search.php">Search</a></li>
          <li class="nav-item"><a class="nav-link" href="KatalogBuku.php">All Books</a></li>
          <?php if (isset($_SESSION['user_id'])): ?>
    <li class="nav-item">
        <a class="nav-link" href="profile.php">Your Profile</a>
    </li>
<?php else: ?>
    <li class="nav-item">
        <a class="nav-link" href="login_register.php">Login / Register</a>
    </li>
<?php endif; ?>
          <li class="nav-item"><a class="nav-link active" href="Contact.php">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>

<div class="container py-5">
  <h2 class="mb-4">Contact GoodBooks</h2>
  <p>If you have any questions, feedback, or support needs, feel free to reach out to us!</p>

  <div class="row mt-4">
    <div class="col-md-6">
      <h5>Our Contact Information</h5>
      <ul class="list-unstyled">
        <li><strong>Email:</strong> support@goodbooks.com</li>
        <li><strong>Phone:</strong> +62 812-3456-7890</li>
        <li><strong>Address:</strong> Jl. Fiksi No. 123, Jakarta, Indonesia</li>
        <li><strong>Hours:</strong> Mon - Fri, 09:00 - 17:00 WIB</li>
      </ul>
    </div>
    <div class="col-md-6">
      <h5>Send Us a Message</h5>
      <form>
        <div class="mb-3">
          <label for="name" class="form-label">Your Name</label>
          <input type="text" class="form-control" id="name" placeholder="Full Name">
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Your Email</label>
          <input type="email" class="form-control" id="email" placeholder="you@example.com">
        </div>
        <div class="mb-3">
          <label for="message" class="form-label">Message</label>
          <textarea class="form-control" id="message" rows="4" placeholder="Type your message here..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
        <small class="d-block text-muted mt-2">* Ini form dummy.</small>
      </form>
    </div>
  </div>
</div>
</body>
</html>
