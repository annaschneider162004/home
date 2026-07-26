<?php
/**
 * Database Connection (PDO Singleton)
 */

require_once dirname(__DIR__) . '/config/database.php';

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Log detailed error server-side, show generic message to users
                error_log('[MusicOfEveryone] DB connection error: ' . $e->getMessage());
                die('<div style="font-family:sans-serif;padding:2rem;background:#fee;border:1px solid #f00;margin:1rem;border-radius:8px;"><h2>Lỗi kết nối cơ sở dữ liệu</h2><p>Không thể kết nối đến cơ sở dữ liệu. Vui lòng kiểm tra file <code>config/database.php</code> và đảm bảo thông tin MySQL chính xác.</p></div>');
            }
        }
        return self::$instance;
    }

    // Thực thi query với prepared statement
    public static function query(string $sql, array $params = []): PDOStatement {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Lấy tất cả rows
    public static function fetchAll(string $sql, array $params = []): array {
        return self::query($sql, $params)->fetchAll();
    }

    // Lấy một row
    public static function fetchOne(string $sql, array $params = []): ?array {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    // Lấy một giá trị đơn
    public static function fetchScalar(string $sql, array $params = []) {
        return self::query($sql, $params)->fetchColumn();
    }

    // Insert và trả về ID vừa insert
    public static function insert(string $table, array $data): int|string {
        $cols = array_keys($data);
        $placeholders = array_map(fn($c) => ":$c", $cols);
        $sql = "INSERT INTO `$table` (" . implode(',', array_map(fn($c) => "`$c`", $cols)) . ") VALUES (" . implode(',', $placeholders) . ")";
        self::query($sql, $data);
        return self::getInstance()->lastInsertId();
    }

    // Update rows
    public static function update(string $table, array $data, string $where, array $whereParams = []): int {
        $sets = array_map(fn($c) => "`$c` = :$c", array_keys($data));
        $sql = "UPDATE `$table` SET " . implode(',', $sets) . " WHERE $where";
        $stmt = self::query($sql, array_merge($data, $whereParams));
        return $stmt->rowCount();
    }

    // Delete rows
    public static function delete(string $table, string $where, array $params = []): int {
        $sql = "DELETE FROM `$table` WHERE $where";
        return self::query($sql, $params)->rowCount();
    }
}
