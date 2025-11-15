<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsersTableToSimple extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $forge = $this->forge;

        // 1) Buat tabel sementara dengan struktur baru (tanpa default CURRENT_TIMESTAMP)
        $forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            // gunakan DATETIME NULL untuk kompatibilitas MySQL
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $forge->addKey('id', true);
        // tambahkan index (non-unique) dulu; unique akan dibuat via ALTER
        $forge->addKey('email');
        $forge->createTable('users_new', true);

        // 2) Salin data dari tabel lama ke tabel baru
        // Asumsi tabel lama bernama `users` dan memiliki kolom:
        // user_id, first_name, last_name, email, password, created_at
        $copySql = "
            INSERT INTO users_new (name, email, password_hash, created_at)
            SELECT
                CONCAT_WS(' ', IFNULL(first_name, ''), IFNULL(last_name, '')) AS name,
                email,
                password AS password_hash,
                COALESCE(created_at, NOW())
            FROM users;
        ";
        // Jika tabel lama tidak ada, query ini bisa gagal — itu normal pada first deploy.
        try {
            $db->query($copySql);
        } catch (\Throwable $e) {
            // jika gagal (mis. tabel lama tidak ada), log dan lanjutkan
            log_message('warning', 'Copy users -> users_new failed or skipped: ' . $e->getMessage());
        }

        // 3) Tambah constraint UNIQUE pada email di tabel baru (via raw query)
        try {
            $db->query("ALTER TABLE users_new ADD UNIQUE INDEX ux_users_new_email (email)");
        } catch (\Throwable $e) {
            // jika index sudah ada atau gagal, catat warning
            log_message('warning', 'Adding unique index ux_users_new_email failed or skipped: ' . $e->getMessage());
        }

        // 4) Drop tabel lama (jika ada) dan rename new -> users
        // Backup: rename old users to users_old_backup jika ada, agar aman
        try {
            // jika tabel users ada, rename dulu sebagai backup
            $tables = $db->listTables();
            if (in_array('users', $tables)) {
                // beri nama backup unik berbasis timestamp
                $backupName = 'users_old_backup_' . date('YmdHis');
                $db->simpleQuery("RENAME TABLE `users` TO `{$backupName}`");
                log_message('info', "Renamed existing users -> {$backupName}");
            }
        } catch (\Throwable $e) {
            log_message('warning', 'Could not rename existing users table: ' . $e->getMessage());
        }

        // Rename users_new -> users
        try {
            $forge->renameTable('users_new', 'users', true);
        } catch (\Throwable $e) {
            log_message('error', 'Failed to rename users_new to users: ' . $e->getMessage());
            throw $e;
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $forge = $this->forge;

        // Membuat tabel lama sederhana kembali (tanpa default CURRENT_TIMESTAMP)
        $forge->addField([
            'user_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'role' => [
                'type' => 'INT',
                'null' => true,
                'default' => 2,
            ],
        ]);
        $forge->addKey('user_id', true);
        $forge->createTable('users_old_recreated', true);

        // Salin dari users (baru) ke users_old_recreated
        try {
            $db->query("
                INSERT INTO users_old_recreated (user_id, first_name, last_name, email, password, created_at, updated_at, role)
                SELECT
                    CONCAT('uid', id) AS user_id,
                    SUBSTRING_INDEX(name, ' ', 1) AS first_name,
                    TRIM(SUBSTRING(name, LENGTH(SUBSTRING_INDEX(name, ' ', 1)) + 2)) AS last_name,
                    email,
                    password_hash AS password,
                    created_at,
                    created_at,
                    2
                FROM users;
            ");
        } catch (\Throwable $e) {
            log_message('warning', 'Copy users -> users_old_recreated failed or skipped: ' . $e->getMessage());
        }

        // Hapus users (baru) dan ganti nama users_old_recreated -> users
        try {
            $forge->dropTable('users', true);
            $forge->renameTable('users_old_recreated', 'users', true);
        } catch (\Throwable $e) {
            log_message('error', 'Down migration failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
