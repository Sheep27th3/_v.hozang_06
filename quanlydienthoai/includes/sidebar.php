<div class="category-menu-wrapper">
    <ul class="category-menu">
        
        <!-- Nút Trang chủ (Cố định ở đầu) -->
        <li class="menu-item">
            <a href="index.php" class="menu-link">
                <i class="fa-solid fa-house text-danger"></i>
                <span class="fw-bold">Trang chủ</span>
            </a>
        </li>

        <?php
        // Lấy tất cả danh mục từ CSDL (bảng categories)
        $stmt_categories = $conn->query("SELECT * FROM categories ORDER BY id ASC");
        while ($cat = $stmt_categories->fetch()):
        ?>
            <!-- Hiển thị từng danh mục lấy từ Admin -->
            <li class="menu-item">
                <a href="index.php?category_id=<?= $cat['id'] ?>" class="menu-link">
                    <i class="fa-solid fa-mobile-screen-button text-danger"></i>
                    <span><?= htmlspecialchars($cat['name']) ?></span>
                    <i class="fa-solid fa-chevron-right arrow-icon"></i>
                </a>
            </li>
        <?php endwhile; ?>

    </ul>
</div>