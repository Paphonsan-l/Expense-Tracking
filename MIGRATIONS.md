# Database Migrations

## การใช้งาน Migration Scripts

Migration Scripts ใช้สำหรับอัปเดตโครงสร้างฐานข้อมูลสำหรับผู้ที่มีฐานข้อมูลเก่าอยู่แล้ว

### วิธีการ Run Migration

#### วิธีที่ 1: ผ่าน phpMyAdmin
1. เข้า phpMyAdmin ที่ http://localhost:8081
2. เข้าฐานข้อมูล `expense_tracking`
3. คลิกแท็บ **SQL**
4. Copy Script จาก Migration File
5. Paste และคลิก **Go**

#### วิธีที่ 2: ผ่าน Docker MySQL CLI
```bash
# เข้า MySQL Container
docker exec -it expense_tracking_mysql bash

# Login MySQL
mysql -u root -proot_password

# Run Migration
source /docker-entrypoint-initdb.d/migration_001_profile_image.sql
exit
```

#### วิธีที่ 3: ผ่าน Command Line
```bash
# จากโฟลเดอร์โปรเจค
docker exec -i expense_tracking_mysql mysql -uroot -proot_password expense_tracking < docker/mysql/migration_001_profile_image.sql
```

---

## รายการ Migration

### migration_001_profile_image.sql
**วันที่**: 2024
**จุดประสงค์**: เปลี่ยนชื่อคอลัมน์ `avatar` เป็น `profile_image` ในตาราง users

**การเปลี่ยนแปลง**:
- เพิ่มคอลัมน์ `profile_image` VARCHAR(255)
- Copy ข้อมูลจาก `avatar` ไปยัง `profile_image`
- (Optional) ลบคอลัมน์ `avatar`

**เหตุผล**: 
- ชื่อคอลัมน์ `profile_image` สื่อความหมายชัดเจนกว่า
- ตรงกับโค้ดใน Controllers และ Views

**Required**: ❌ ไม่จำเป็นสำหรับ Fresh Install (ฐานข้อมูลใหม่)  
**Required**: ✅ จำเป็นสำหรับผู้ใช้ที่มีข้อมูลเก่าอยู่แล้ว

---

## การตรวจสอบหลัง Migration

### ตรวจสอบโครงสร้างตาราง
```sql
DESCRIBE users;
```

คาดหวังผลลัพธ์:
```
+---------------+--------------+------+-----+-------------------+
| Field         | Type         | Null | Key | Default           |
+---------------+--------------+------+-----+-------------------+
| id            | int          | NO   | PRI | NULL              |
| username      | varchar(50)  | NO   | UNI | NULL              |
| email         | varchar(100) | NO   | UNI | NULL              |
| password      | varchar(255) | NO   |     | NULL              |
| full_name     | varchar(100) | NO   |     | NULL              |
| profile_image | varchar(255) | YES  |     | NULL              |
| created_at    | timestamp    | YES  |     | CURRENT_TIMESTAMP |
| updated_at    | timestamp    | YES  |     | CURRENT_TIMESTAMP |
+---------------+--------------+------+-----+-------------------+
```

### ตรวจสอบข้อมูล
```sql
SELECT id, username, email, profile_image FROM users LIMIT 5;
```

---

## Rollback (ย้อนกลับ)

หากต้องการย้อนกลับจาก Migration:

```sql
USE expense_tracking;

-- Restore avatar column
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) DEFAULT NULL AFTER full_name;

-- Copy data back
UPDATE users 
SET avatar = profile_image 
WHERE profile_image IS NOT NULL;

-- Remove profile_image (if needed)
-- ALTER TABLE users DROP COLUMN profile_image;
```

---

## Best Practices

1. **Backup ก่อน Migrate**: สำรองฐานข้อมูลก่อนทุกครั้ง
   ```bash
   docker exec expense_tracking_mysql mysqldump -uroot -proot_password expense_tracking > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **ทดสอบใน Development ก่อน**: ทดสอบ Migration ใน environment ทดสอบก่อน

3. **อ่าน Migration Script**: อ่านและเข้าใจ Script ก่อน Run

4. **เก็บ Migration History**: บันทึกว่า Run Migration ไหนไปแล้วบ้าง

---

## FAQ

**Q: ต้อง Run Migration ทุกครั้งไหม?**  
A: ไม่ใช่ ถ้าคุณติดตั้งใหม่ (Fresh Install) ไม่ต้อง Run เพราะ `init.sql` มีโครงสร้างล่าสุดอยู่แล้ว

**Q: จะรู้ได้ยังไงว่าต้อง Run Migration?**  
A: ถ้าระบบ Error เกี่ยวกับคอลัมน์ `profile_image not found` แปลว่าฐานข้อมูลของคุณเป็นเวอร์ชันเก่า

**Q: Backup อัตโนมัติมีไหม?**  
A: ไม่มีในเวอร์ชันนี้ แนะนำให้สร้าง Cron Job สำหรับ backup อัตโนมัติ

---

**Last Updated**: 2024  
**Migration Version**: 1.0
