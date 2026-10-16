<?php
class Model
{
    public static function run(string $sql, array $p = []): PDOStatement
    {
        $st = Database::get()->prepare($sql);
        $st->execute($p);
        return $st;
    }
    public static function all(string $sql, array $p = []): array { return self::run($sql, $p)->fetchAll(); }
    public static function one(string $sql, array $p = []): ?array { $r = self::run($sql, $p)->fetch(); return $r ?: null; }
    public static function val(string $sql, array $p = []) { return self::run($sql, $p)->fetchColumn(); }
    public static function exec(string $sql, array $p = []): int { return self::run($sql, $p)->rowCount(); }

    public static function insert(string $table, array $data): int
    {
        $cols = '`' . implode('`,`', array_keys($data)) . '`';
        $ph = implode(',', array_fill(0, count($data), '?'));
        self::run("INSERT INTO `$table` ($cols) VALUES ($ph)", array_values($data));
        return (int) Database::get()->lastInsertId();
    }

    public static function update(string $table, array $data, int $id): void
    {
        $set = implode(',', array_map(fn($c) => "`$c`=?", array_keys($data)));
        self::run("UPDATE `$table` SET $set WHERE id=?", array_merge(array_values($data), [$id]));
    }
}
