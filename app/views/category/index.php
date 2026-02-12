<?php
$title = 'จัดการหมวดหมู่';
$currentPage = 'category';
require_once BASE_PATH . '/app/views/layouts/header.php';
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>

    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>
                    <i class="fas fa-tags"></i>
                    จัดการหมวดหมู่
                </h1>
            </div>

            <div class="row">
                <!-- Expense Categories -->
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header" style="position: relative; justify-content: center;">
                            <h3
                                style="position: absolute; left: 0; right: 0; text-align: center; pointer-events: none;">
                                <i class="fas fa-shopping-cart"></i>
                                หมวดหมู่รายจ่าย
                            </h3>
                            <button onclick="openAddModal('expense')" class="btn btn-primary"
                                style="margin-left: auto; position: relative; z-index: 1;">
                                <i class="fas fa-plus"></i> เพิ่มหมวดหมู่
                            </button>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;" class="text-center">ไอคอน</th>
                                            <th>ชื่อหมวดหมู่</th>
                                            <th class="text-center" style="width: 100px;">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="expense-categories">
                                        <?php foreach ($data['expense_categories'] as $cat): ?>
                                            <tr id="cat-<?php echo $cat['id']; ?>">
                                                <td class="text-center">
                                                    <div class="category-icon mx-auto"
                                                        style="background-color: <?php echo htmlspecialchars($cat['color']); ?>">
                                                        <i class="fas fa-<?php echo htmlspecialchars($cat['icon']); ?>"></i>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                                <td class="text-center">
                                                    <button
                                                        onclick="openEditModal(<?php echo $cat['id']; ?>, '<?php echo htmlspecialchars($cat['name']); ?>', '<?php echo htmlspecialchars($cat['type']); ?>', '<?php echo htmlspecialchars($cat['icon']); ?>', '<?php echo htmlspecialchars($cat['color']); ?>')"
                                                        class="btn btn-sm btn-warning btn-icon" title="แก้ไข">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="deleteCategory(<?php echo $cat['id']; ?>)"
                                                        class="btn btn-sm btn-danger btn-icon" title="ลบ">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Income Categories -->
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header" style="position: relative; justify-content: center;">
                            <h3
                                style="position: absolute; left: 0; right: 0; text-align: center; pointer-events: none;">
                                <i class="fas fa-hand-holding-dollar"></i>
                                หมวดหมู่รายรับ
                            </h3>
                            <button onclick="openAddModal('income')" class="btn btn-primary"
                                style="margin-left: auto; position: relative; z-index: 1;">
                                <i class="fas fa-plus"></i> เพิ่มหมวดหมู่
                            </button>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;" class="text-center">ไอคอน</th>
                                            <th>ชื่อหมวดหมู่</th>
                                            <th class="text-center" style="width: 100px;">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="income-categories">
                                        <?php foreach ($data['income_categories'] as $cat): ?>
                                            <tr id="cat-<?php echo $cat['id']; ?>">
                                                <td class="text-center">
                                                    <div class="category-icon mx-auto"
                                                        style="background-color: <?php echo htmlspecialchars($cat['color']); ?>">
                                                        <i class="fas fa-<?php echo htmlspecialchars($cat['icon']); ?>"></i>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                                <td class="text-center">
                                                    <button
                                                        onclick="openEditModal(<?php echo $cat['id']; ?>, '<?php echo htmlspecialchars($cat['name']); ?>', '<?php echo htmlspecialchars($cat['type']); ?>', '<?php echo htmlspecialchars($cat['icon']); ?>', '<?php echo htmlspecialchars($cat['color']); ?>')"
                                                        class="btn btn-sm btn-warning btn-icon" title="แก้ไข">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="deleteCategory(<?php echo $cat['id']; ?>)"
                                                        class="btn btn-sm btn-danger btn-icon" title="ลบ">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Add Category Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>เพิ่มหมวดหมู่</h2>
            <span class="close" onclick="closeModal('addModal')">&times;</span>
        </div>
        <form id="addForm" onsubmit="addCategory(event)">
            <div class="modal-body">
                <input type="hidden" id="add_type" name="type">

                <div class="form-group">
                    <label for="add_name">ชื่อหมวดหมู่ <span style="color: red;">*</span></label>
                    <input type="text" id="add_name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="add_icon">ไอคอน (Font Awesome) <span style="color: red;">*</span></label>
                    <input type="text" id="add_icon" name="icon" class="form-control"
                        placeholder="เช่น: home, car, food, wallet" required>
                    <small style="color: #888;">ดูไอคอนทั้งหมดที่ <a href="https://fontawesome.com/icons"
                            target="_blank">fontawesome.com</a></small>
                </div>

                <div class="form-group">
                    <label for="add_color">สี <span style="color: red;">*</span></label>
                    <input type="color" id="add_color" name="color" class="form-control" value="#3498db" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">ยกเลิก</button>
                <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>แก้ไขหมวดหมู่</h2>
            <span class="close" onclick="closeModal('editModal')">&times;</span>
        </div>
        <form id="editForm" onsubmit="editCategory(event)">
            <div class="modal-body">
                <input type="hidden" id="edit_id" name="id">
                <input type="hidden" id="edit_type" name="type">

                <div class="form-group">
                    <label for="edit_name">ชื่อหมวดหมู่ <span style="color: red;">*</span></label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_icon">ไอคอน (Font Awesome) <span style="color: red;">*</span></label>
                    <input type="text" id="edit_icon" name="icon" class="form-control"
                        placeholder="เช่น: home, car, food, wallet" required>
                </div>

                <div class="form-group">
                    <label for="edit_color">สี <span style="color: red;">*</span></label>
                    <input type="color" id="edit_color" name="color" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">ยกเลิก</button>
                <button type="submit" class="btn btn-warning">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>

<script>
    function openAddModal(type) {
        document.getElementById('add_type').value = type;
        document.getElementById('addForm').reset();
        document.getElementById('addModal').style.display = 'block';
    }

    function openEditModal(id, name, type, icon, color) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_icon').value = icon;
        document.getElementById('edit_color').value = color;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    async function addCategory(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name'),
            type: formData.get('type'),
            icon: formData.get('icon'),
            color: formData.get('color')
        };

        try {
            const response = await fetch('/category/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('เพิ่มหมวดหมู่สำเร็จ');
                location.reload();
            } else {
                alert('เกิดข้อผิดพลาด: ' + (result.message || 'ไม่สามารถเพิ่มข้อมูลได้'));
            }
        } catch (error) {
            alert('เกิดข้อผิดพลาด: ' + error.message);
        }
    }

    async function editCategory(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const id = formData.get('id');
        const data = {
            name: formData.get('name'),
            type: formData.get('type'),
            icon: formData.get('icon'),
            color: formData.get('color')
        };

        try {
            const response = await fetch('/category/update/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('แก้ไขหมวดหมู่สำเร็จ');
                location.reload();
            } else {
                alert('เกิดข้อผิดพลาด: ' + (result.message || 'ไม่สามารถแก้ไขข้อมูลได้'));
            }
        } catch (error) {
            alert('เกิดข้อผิดพลาด: ' + error.message);
        }
    }

    async function deleteCategory(id) {
        if (!confirm('คุณต้องการลบหมวดหมู่นี้หรือไม่?')) {
            return;
        }

        try {
            const response = await fetch('/category/delete/' + id, {
                method: 'POST'
            });

            const result = await response.json();

            if (result.success) {
                alert('ลบหมวดหมู่สำเร็จ');
                location.reload();
            } else {
                alert('เกิดข้อผิดพลาด: ' + (result.message || 'ไม่สามารถลบข้อมูลได้'));
            }
        } catch (error) {
            alert('เกิดข้อผิดพลาด: ' + error.message);
        }
    }

    // Close modal when clicking outside
    window.onclick = function (event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>