<?php
/**
 * Validator Helper
 */

class Validator {
    private $errors = [];
    
    /**
     * Validate required field
     */
    public function required($field, $value, $message = null) {
        if (empty($value) && $value !== '0') {
            $this->errors[$field] = $message ?? "กรุณากรอก {$field}";
            return false;
        }
        return true;
    }
    
    /**
     * Validate email
     */
    public function email($field, $value, $message = null) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "รูปแบบอีเมลไม่ถูกต้อง";
            return false;
        }
        return true;
    }
    
    /**
     * Validate minimum length
     */
    public function minLength($field, $value, $length, $message = null) {
        if (strlen($value) < $length) {
            $this->errors[$field] = $message ?? "{$field} ต้องมีอย่างน้อย {$length} ตัวอักษร";
            return false;
        }
        return true;
    }
    
    /**
     * Validate maximum length
     */
    public function maxLength($field, $value, $length, $message = null) {
        if (strlen($value) > $length) {
            $this->errors[$field] = $message ?? "{$field} ต้องไม่เกิน {$length} ตัวอักษร";
            return false;
        }
        return true;
    }
    
    /**
     * Validate numeric
     */
    public function numeric($field, $value, $message = null) {
        if (!is_numeric($value)) {
            $this->errors[$field] = $message ?? "{$field} ต้องเป็นตัวเลขเท่านั้น";
            return false;
        }
        return true;
    }
    
    /**
     * Validate match (for password confirmation)
     */
    public function match($field, $value, $compareValue, $message = null) {
        if ($value !== $compareValue) {
            $this->errors[$field] = $message ?? "ข้อมูลไม่ตรงกัน";
            return false;
        }
        return true;
    }
    
    /**
     * Validate unique (check database)
     */
    public function unique($field, $value, $table, $column, $excludeId = null, $message = null) {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = :value";
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }
        
        $stmt = $db->prepare($sql);
        $params = ['value' => $value];
        
        if ($excludeId !== null) {
            $params['exclude_id'] = $excludeId;
        }
        
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            $this->errors[$field] = $message ?? "{$value} ถูกใช้งานแล้ว";
            return false;
        }
        return true;
    }
    
    /**
     * Validate file upload
     */
    public function file($field, $file, $message = null) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[$field] = $message ?? "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
            return false;
        }
        
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            $maxMB = MAX_FILE_SIZE / 1024 / 1024;
            $this->errors[$field] = "ขนาดไฟล์ต้องไม่เกิน {$maxMB} MB";
            return false;
        }
        
        // Check file type
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedTypes = explode(',', ALLOWED_FILE_TYPES);
        
        if (!in_array($ext, $allowedTypes)) {
            $this->errors[$field] = "อนุญาตเฉพาะไฟล์นามสกุล: " . implode(', ', $allowedTypes);
            return false;
        }
        
        return true;
    }
    
    /**
     * Sanitize input
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Get all errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get specific error
     */
    public function getError($field) {
        return $this->errors[$field] ?? null;
    }
    
    /**
     * Check if validation passed
     */
    public function passes() {
        return empty($this->errors);
    }
    
    /**
     * Check if validation failed
     */
    public function fails() {
        return !$this->passes();
    }
}
