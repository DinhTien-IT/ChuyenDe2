<?php
session_start();
include 'db.php';

// Xử lý thêm sản phẩm vào giỏ hàng khi có POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantityToAdd = max(1, (int)($_POST['quantity'] ?? 1));

    // Lấy sản phẩm từ DB để xác thực (phòng trường hợp giả mạo)
    $stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantityToAdd;
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantityToAdd
            ];
        }
    }

    // Chuyển hướng để tránh submit lại form khi reload trang
    header("Location: index.php");
    exit();
}

// Lấy danh sách sản phẩm để hiển thị
$products = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 8");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>3T Furniture - Trang chủ</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
  />
</head>
<body class="bg-gray-50 font-sans">

<!-- Header -->
<?php include 'includes/header.php'; ?>
<div class="w-full">
  <div
    class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4 px-4 py-3"
  >
    <a href="index.php" class="flex items-center gap-2">
      <img src="logo.jpg" alt="3T Furniture Logo" class="h-12 w-auto" />
    </a>

    <form
      class="flex flex-grow max-w-xl w-full border border-blue-400 rounded-full overflow-hidden"
      method="GET"
      action="search.php"
    >
      <input
        type="text"
        name="q"
        placeholder="Tìm kiếm sản phẩm..."
        class="flex-grow px-4 py-2 border-none outline-none focus:ring-0 shadow-none text-sm bg-white"
      />
      <button
        type="submit"
        class="bg-blue-500 text-white px-4 py-2 hover:bg-blue-600 transition text-sm font-medium"
      >
        Tìm
      </button>
    </form>

    <div class="flex items-center gap-2 shrink-0">
      <button
        onclick="toggleContactModal()"
        class="text-blue-600 bg-transparent px-4 py-2 rounded-full hover:bg-blue-50 transition text-sm font-medium"
      >
        📞 Liên hệ
      </button>

      <a
        href="https://www.facebook.com"
        target="_blank"
        class="text-blue-600 bg-transparent px-4 py-2 rounded-full hover:bg-blue-50 transition text-sm font-medium"
      >
        🌐 Facebook
      </a>

      <a
        href="chat.php"
        class="text-blue-600 bg-transparent px-4 py-2 rounded-full hover:bg-blue-50 transition text-sm font-medium"
      >
        💬 Nhắn tin
      </a>

      <a
        href="cart.php"
        class="relative flex items-center justify-center w-10 h-10 bg-transparent hover:bg-blue-50 rounded-full transition"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-blue-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7h13l-1.5-7M7 13h10m-6 9a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2z"
          />
        </svg>
        <?php
        $cartCount = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $cartCount += $item['quantity'];
            }
        }
        ?>
        <span
          id="cart-count"
          class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
          ><?= $cartCount ?></span
        >
      </a>
    </div>
  </div>
</div>

<!-- Carousel -->
<div class="relative w-full max-w-7xl mx-auto mt-4">
  <div class="relative overflow-hidden rounded-lg shadow-lg h-[700px]">
    <div id="carousel" class="relative w-full h-full">
      <img
        src="banner1.jpg"
        class="carousel-slide absolute inset-0 w-full h-full object-cover opacity-100 transition-opacity duration-700"
        alt="Banner 1"
      />
      <img
        src="banner2.jpg"
        class="carousel-slide absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-700"
        alt="Banner 2"
      />
      <img
        src="banner3.jpg"
        class="carousel-slide absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-700"
        alt="Banner 3"
      />
    </div>
    <button
      onclick="prevSlide()"
      class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-black bg-opacity-40 text-white px-3 py-2 rounded-full hover:bg-opacity-60 transition"
    >
      ❮
    </button>
    <button
      onclick="nextSlide()"
      class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-black bg-opacity-40 text-white px-3 py-2 rounded-full hover:bg-opacity-60 transition"
    >
      ❯
    </button>
    <div class="absolute bottom-3 w-full flex justify-center gap-2">
      <span
        class="indicator w-3 h-3 bg-white rounded-full opacity-70 cursor-pointer"
        onclick="goToSlide(0)"
      ></span>
      <span
        class="indicator w-3 h-3 bg-white rounded-full opacity-50 cursor-pointer"
        onclick="goToSlide(1)"
      ></span>
      <span
        class="indicator w-3 h-3 bg-white rounded-full opacity-50 cursor-pointer"
        onclick="goToSlide(2)"
      ></span>
    </div>
  </div>
</div>

<!-- Giới thiệu -->
<section class="max-w-7xl mx-auto p-6 text-center mt-12">
  <h2 class="text-4xl font-extrabold mb-6 text-gray-800">
    Giới thiệu về 3T Furniture
  </h2>
  <p class="text-gray-700 text-lg leading-relaxed max-w-6xl mx-auto mb-8">
    3T Furniture tự hào là đơn vị chuyên cung cấp các sản phẩm nội thất
    <span class="font-semibold text-black-600">chất lượng cao</span>,
    được thiết kế hiện đại, tinh tế và phù hợp với nhiều không gian sống –
    từ căn hộ chung cư, nhà phố đến biệt thự cao cấp.
  </p>
  <div class="flex justify-center mb-8">
    <img
      src="assets/images/about.jpg"
      alt="Giới thiệu 3T Furniture"
      class="rounded-lg shadow-lg w-full max-w-4xl object-cover"
    />
  </div>
</section>

<!-- Giá trị -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto px-6">
  <div
    class="bg-white shadow-lg rounded-2xl p-8 text-center hover:shadow-2xl transform hover:scale-105 transition"
  >
    <div
      class="w-20 h-20 mx-auto mb-6 flex items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-blue-700 text-white text-4xl shadow"
    >
      <i class="fas fa-medal"></i>
    </div>
    <h3 class="text-2xl font-bold mb-3 text-gray-800">Chất lượng hàng đầu</h3>
    <p class="text-gray-600 leading-relaxed">
      Sản phẩm được kiểm định nghiêm ngặt trước khi đến tay khách hàng.
    </p>
  </div>
  <div
    class="bg-white shadow-lg rounded-2xl p-8 text-center hover:shadow-2xl transform hover:scale-105 transition"
  >
    <div
      class="w-20 h-20 mx-auto mb-6 flex items-center justify-center rounded-full bg-gradient-to-r from-green-400 to-green-600 text-white text-4xl shadow"
    >
      <i class="fas fa-tags"></i>
    </div>
    <h3 class="text-2xl font-bold mb-3 text-gray-800">Giá cả hợp lý</h3>
    <p class="text-gray-600 leading-relaxed">
      Mang đến mức giá cạnh tranh, phù hợp với nhiều phân khúc khách hàng.
    </p>
  </div>
  <div
    class="bg-white shadow-lg rounded-2xl p-8 text-center hover:shadow-2xl transform hover:scale-105 transition"
  >
    <div
      class="w-20 h-20 mx-auto mb-6 flex items-center justify-center rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-4xl shadow"
    >
      <i class="fas fa-handshake"></i>
    </div>
    <h3 class="text-2xl font-bold mb-3 text-gray-800">Hậu mãi tận tâm</h3>
    <p class="text-gray-600 leading-relaxed">
      Dịch vụ bảo hành và chăm sóc khách hàng chuyên nghiệp, luôn đồng hành
      cùng bạn.
    </p>
  </div>
</div>

<!-- Danh sách sản phẩm -->
<div class="max-w-7xl mx-auto p-6">
  <h3 class="text-2xl font-bold mb-6 text-center">Sản phẩm nổi bật</h3>
  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php if ($products->num_rows > 0): ?>
      <?php while ($row = $products->fetch_assoc()): ?>
        <div
          class="bg-white rounded-lg shadow hover:shadow-lg p-4 flex flex-col items-center transition"
        >
          <img
            src="uploads/<?= htmlspecialchars($row['image']) ?>"
            alt="<?= htmlspecialchars($row['name']) ?>"
            class="rounded mb-3 w-full h-48 object-cover"
          />
          <h3 class="text-lg font-semibold"><?= htmlspecialchars($row['name']) ?></h3>
          <p class="text-gray-600">
            Giá: <?= number_format($row['price'], 0, ',', '.') ?>đ
          </p>
          <!-- Form thêm vào giỏ hàng -->
          <form method="POST" class="flex gap-2 mt-3">
            <input type="hidden" name="product_id" value="<?= $row['id'] ?>" />
            <input
              type="number"
              name="quantity"
              value="1"
              min="1"
              class="w-16 px-2 py-1 border rounded text-center"
            />
            <button
              type="submit"
              name="add_to_cart"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
            >
              Thêm
            </button>
          </form>
          <a
            href="product.php?id=<?= $row['id'] ?>"
            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition mt-2"
            >Xem</a
          >
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="col-span-4 text-center text-gray-500">Chưa có sản phẩm nào!</p>
    <?php endif; ?>
  </div>
</div>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Scripts -->
<script>
  let index = 0;
  const slides = document.querySelectorAll('.carousel-slide');
  const indicators = document.querySelectorAll('.indicator');
  const totalSlides = slides.length;

  function showSlide(i) {
    slides.forEach((slide, idx) => {
      slide.style.opacity = idx === i ? '1' : '0';
    });
    indicators.forEach((dot, idx) => {
      dot.classList.toggle('opacity-70', idx === i);
      dot.classList.toggle('opacity-50', idx !== i);
    });
  }
  function nextSlide() {
    index = (index + 1) % totalSlides;
    showSlide(index);
  }
  function prevSlide() {
    index = (index - 1 + totalSlides) % totalSlides;
    showSlide(index);
  }
  function goToSlide(i) {
    index = i;
    showSlide(index);
  }
  setInterval(nextSlide, 4000);
  showSlide(index);

  function toggleContactModal() {
    alert('Chức năng liên hệ sẽ được cập nhật sớm!');
  }
</script>
</body>
</html>
