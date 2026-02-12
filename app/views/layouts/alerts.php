<?php
// Check for success message
if ($success = Session::flash('success')) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '" . htmlspecialchars($success, ENT_QUOTES) . "',
                showConfirmButton: false,
                timer: 1500
            });
        });
    </script>";
}

// Check for error message
if ($error = Session::flash('error')) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '" . htmlspecialchars($error, ENT_QUOTES) . "',
                confirmButtonText: 'ตกลง'
            });
        });
    </script>";
}

// Check for warning message
if ($warning = Session::flash('warning')) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'warning',
                title: 'คำเตือน!',
                text: '" . htmlspecialchars($warning, ENT_QUOTES) . "',
                confirmButtonText: 'ตกลง'
            });
        });
    </script>";
}

// Check for info message
if ($info = Session::flash('info')) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'info',
                title: 'ข้อมูล',
                text: '" . htmlspecialchars($info, ENT_QUOTES) . "',
                confirmButtonText: 'ตกลง'
            });
        });
    </script>";
}
?>