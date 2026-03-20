<style>
#addProductModal .modal-content {
    height: 80vh;
}

#addProductModal .modal-body {
    overflow-y: scroll;
    max-height: calc(80vh - 130px);
}

[id^="editProductModal"] .modal-content {
    height: 80vh;
}

[id^="editProductModal"] .modal-body {
    overflow-y: scroll;
    max-height: calc(80vh - 130px);
}

[id^="viewProductModal"] .modal-content {
    max-height: 80vh;
}

[id^="viewProductModal"] .modal-body {
    overflow-y: scroll;
    max-height: calc(80vh - 100px);
}
</style>
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/jquery-3.7.1.min.js"></script>
<script src="js/validate.js"></script>
<?php
include_once 'db_config.php';

// Step 1: Open database connection so product data can be fetched.
?>

<div class="container">
    <br>
    <h1 class="text-center mb-4">Product Management</h1>


    <!-- Display success and error Messages -->
    <?php if (isset($_COOKIE['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_COOKIE['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_COOKIE['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_COOKIE['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Add product Button -->

    <div class="row">
        <div class="col-md-2">
            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                data-bs-target="#addProductModal">
                Add New Product
            </button>
        </div>

        <!-- Search Box -->

        <div class="col-md-10">
            <input type="text" id="searchInput" class="form-control" placeholder="Search products..."
                value="<?= isset($_GET['search']) ? htmlspecialchars(trim($_GET['search']), ENT_QUOTES) : '' ?>">
        </div>
    </div>

    <!-- Add product Modal -->

    <br>

    <?php
    include_once 'db_config.php';

    // Step 2: Run the read stored procedure to fetch all products.
    $selectStmt = $connection->prepare("CALL sp_products_read()");
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    // Step 3: Flush remaining result sets from stored procedure calls.
    flush_stored_results($connection);

    ?>
    <table class="table table-bordered table-striped align-middle" id="productsTable">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Category</th>
                <th>Description</th>
                <th>Image</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="productsTableBody">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= (int) $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['price']) ?></td>
                <td><?= htmlspecialchars($row['stock']) ?></td>
                <td><?= htmlspecialchars($row['category_id']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>
                    <?php if (!empty($row['image'])) { ?>
                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>"
                        width="100">
                    <?php } ?>
                </td>
                <td><?= (strtolower((string) $row['status']) === 'active' || $row['status'] == 1) ? 'Active' : 'Inactive' ?>
                </td>
                <td>
                    <!-- View Product Button -->
                    <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal"
                        data-bs-target="#viewProductModal<?= (int) $row['id'] ?>">View</button>

                    <!-- View Product Modal -->

                    <!-- Edit Product Button -->
                    <button class="btn btn-sm btn-warning mb-1" data-bs-toggle="modal"
                        data-bs-target="#editProductModal<?= (int) $row['id'] ?>">Edit</button>
                    <!-- Edit product modal -->


                    <!-- Delete Product Button -->
                    <button class="btn btn-sm btn-danger mb-1" data-bs-toggle="modal"
                        data-bs-target="#deleteProductModal<?= (int) $row['id'] ?>">Delete</button>

                    <!-- Delete Product Modal -->


                    <!-- Change Status Button -->
                    <button class="btn btn-sm btn-secondary mb-1" type="button" data-bs-toggle="modal"
                        data-bs-target="#changeStatusModal<?= (int) $row['id'] ?>">
                        <?= (strtolower((string) $row['status']) === 'active' || $row['status'] == 1) ? 'Deactivate' : 'Activate' ?>
                    </button>

                    <!-- Change Status Modal -->


                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="9" class="text-center text-muted">No products found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>


    <br>
    <br>
</div>