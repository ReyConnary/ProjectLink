<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GoodBooks</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>

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
          <li class="nav-item"><a class="nav-link active" href="GoodBooks.php">Home</a></li>
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
          <li class="nav-item"><a class="nav-link" href="Contact.php">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>


  <div class="bg-light py-5 text-center">
    <div class="container">
        <h1 class="display-5">Welcome to GoodBooks</h1>
      <p class="lead">Browse and buy your favorite books online!</p>
    </div>
  </div>

  <div class="container py-5">
  <div class="row">
    <div class="col-12">
      <p class="lead">GoodBooks is a bookstore with a singular, passionate mission: to sell books—and only books. Unlike many modern chains that dilute their shelves with gadgets, games, or gimmicks, GoodBooks remains true to its roots by offering only two timeless categories: comics and novels. We believe these forms of storytelling are more than just entertainment—they're a way of preserving imagination, culture, and the human experience across generations.

The story of GoodBooks begins in the aftermath of one of the darkest chapters in modern history: World War II. Founded in 1947 by a married couple, John and Jane Book, the company’s origins are as unique as they are inspiring. During the war, while others stockpiled food, medicine, and other necessities, the Books collected what they feared the world might lose forever—books. From dusty volumes in abandoned libraries to priceless editions tucked away in distant corners of Europe, they amassed an astonishing private archive, driven by a singular belief: the loss of knowledge is more terrifying than the loss of life.

When the war ended, and the world began to rebuild, the Books opened the doors of a humble shack filled wall-to-wall with their beloved collection. They named it GoodBooks, a simple name rooted in truth, and began selling to war-weary citizens who were desperate for comfort, escape, and understanding. The shop became a beacon of hope, and its reputation spread quickly as a sanctuary where people could find not just stories, but healing.

From that small, book-filled shack, GoodBooks flourished. As postwar society evolved, so did the company, expanding to meet the growing hunger for literature in a recovering world. But it wasn’t just Americans who took notice. The Books had not only collected English literature—they had preserved stories from all over the world, in multiple languages and styles. As word spread, foreign countries began to purchase en masse from GoodBooks as well, establishing its status as a truly global source of knowledge and culture.

Today, GoodBooks has become a nation-spanning brand with over 100 outlets across the United States and more than 10,000 stores internationally. Though its reach has expanded far beyond what John and Jane Book could have imagined, the heart of the company remains the same: a bookstore born out of love for literature, forged in the ashes of war, and sustained by generations who understand that stories matter.

Whether you're seeking the humor and wonder of a comic or the rich depth of a novel, GoodBooks invites you to step into a legacy that began with two visionaries and a warehouse of words. Welcome to the story that never ends.</p>
    </div>
  </div>
</div>

  <footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
      <p class="mb-0">© 2025 GoodBooks. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
