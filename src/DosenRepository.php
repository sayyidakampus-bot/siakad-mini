// Kode program untuk mengambil data dosen dari database
<?php

require_once __DIR__ . '/../config/database.php';

class DosenRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(
    $search = '',
    $programStudi = '',
    $status = '',
    $sort = 'nama',
    $direction = 'ASC',
    $limit = 5,
    $offset = 0
)
{
    $query = "
        SELECT
            dosen.*,

            GROUP_CONCAT(
                mata_kuliah.nama
                SEPARATOR ', '
            ) as mata_kuliah_list

        FROM dosen

        LEFT JOIN dosen_matakuliah
            ON dosen.id = dosen_matakuliah.dosen_id

        LEFT JOIN mata_kuliah
            ON mata_kuliah.id = dosen_matakuliah.matakuliah_id

        WHERE dosen.deleted_at IS NULL

        AND (
            dosen.nama LIKE :search1
            OR dosen.nidn LIKE :search2
        )
    ";

    if ($programStudi !== '') {

        $query .= "
            AND dosen.program_studi = :program_studi
        ";
    }

    if ($status !== '') {

        $query .= "
            AND dosen.status = :status
        ";
    }

    $allowedSort = ['id', 'nama', 'nidn'];

    if (!in_array($sort, $allowedSort)) {
        $sort = 'nama';
    }

    $direction = strtoupper($direction);

    if (!in_array($direction, ['ASC', 'DESC'])) {
        $direction = 'ASC';
    }

    $query .= "
        GROUP BY dosen.id

        ORDER BY dosen.$sort $direction

        LIMIT :limit
        OFFSET :offset
    ";

    $stmt = $this->db->prepare($query);

    $searchTerm = "%$search%";

    $stmt->bindValue(':search1', $searchTerm);

    $stmt->bindValue(':search2', $searchTerm);

    if ($programStudi !== '') {

        $stmt->bindValue(
            ':program_studi',
            $programStudi
        );
    }

    if ($status !== '') {

        $stmt->bindValue(
            ':status',
            $status
        );
    }

    $stmt->bindValue(
        ':limit',
        (int)$limit,
        PDO::PARAM_INT
    );

    $stmt->bindValue(
        ':offset',
        (int)$offset,
        PDO::PARAM_INT
    );

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function create($data)
    {
        $query = "
            INSERT INTO dosen
            (nidn, nama, email, program_studi, foto, status)
            VALUES
            (?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            $data['nidn'],
            $data['nama'],
            $data['email'],
            $data['program_studi'],
            $data['foto'],
            $data['status']
        ]);
    }
    public function find($id)
{
    $query = "
        SELECT *
        FROM dosen
        WHERE id = ?
    ";

    $stmt = $this->db->prepare($query);

    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function update($id, $data)
{
    $query = "
        UPDATE dosen
        SET
            nidn = ?,
            nama = ?,
            email = ?,
            program_studi = ?,
            foto = ?,
            status = ?
        WHERE id = ?
    ";

    $stmt = $this->db->prepare($query);

    return $stmt->execute([
        $data['nidn'],
        $data['nama'],
        $data['email'],
        $data['program_studi'],
        $data['foto'],
        $data['status'],
        $id
    ]);
}

public function softDelete($id)
{
    $query = "
        UPDATE dosen
        SET deleted_at = NOW()
        WHERE id = ?
    ";

    $stmt = $this->db->prepare($query);

    return $stmt->execute([$id]);
}

public function countData($search = '', $programStudi = '', $status = '')
{
    $query = "
        SELECT COUNT(DISTINCT dosen.id) as total
        FROM dosen
        LEFT JOIN dosen_matakuliah
            ON dosen.id = dosen_matakuliah.dosen_id
        WHERE dosen.deleted_at IS NULL
        AND (
            dosen.nama LIKE :search1
            OR dosen.nidn LIKE :search2
        )
    ";

    if ($programStudi !== '') {
        $query .= " AND dosen.program_studi = :program_studi ";
    }

    if ($status !== '') {
        $query .= " AND dosen.status = :status ";
    }

    $stmt = $this->db->prepare($query);

    $searchTerm = "%$search%";
    $stmt->bindValue(':search1', $searchTerm);
    $stmt->bindValue(':search2', $searchTerm);

    if ($programStudi !== '') {
        $stmt->bindValue(':program_studi', $programStudi);
    }

    if ($status !== '') {
        $stmt->bindValue(':status', $status);
    }

    $stmt->execute();

    return $stmt->fetchColumn();
}

public function trash()
{
    $query = "
        SELECT *
        FROM dosen
        WHERE deleted_at IS NOT NULL
        ORDER BY deleted_at DESC
    ";

    $stmt = $this->db->prepare($query);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function restore($id)
{
    $query = "
        UPDATE dosen
        SET deleted_at = NULL
        WHERE id = ?
    ";

    $stmt = $this->db->prepare($query);

    return $stmt->execute([$id]);
}

public function getAllMataKuliah()
{
    $query = "
        SELECT *
        FROM mata_kuliah
        ORDER BY nama ASC
    ";

    $stmt = $this->db->prepare($query);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getMataKuliahByDosen($dosenId)
{
    $query = "
        SELECT matakuliah_id
        FROM dosen_matakuliah
        WHERE dosen_id = ?
    ";

    $stmt = $this->db->prepare($query);

    $stmt->execute([$dosenId]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

public function saveMataKuliah($dosenId, $mataKuliahIds)
{
    try {

        $this->db->beginTransaction();

        $deleteQuery = "
            DELETE FROM dosen_matakuliah
            WHERE dosen_id = ?
        ";

        $deleteStmt = $this->db->prepare($deleteQuery);

        $deleteStmt->execute([$dosenId]);

        $insertQuery = "
            INSERT INTO dosen_matakuliah
            (dosen_id, matakuliah_id, semester)

            VALUES (?, ?, ?)
        ";

        $insertStmt = $this->db->prepare($insertQuery);

        foreach ($mataKuliahIds as $mkId) {

            $insertStmt->execute([
                $dosenId,
                $mkId,
                'Ganjil'
            ]);
        }

        $this->db->commit();

    } catch (Exception $e) {

        $this->db->rollBack();

        die($e->getMessage());
    }
}

public function dashboardStats()
{
    $query = "
        SELECT
            program_studi,
            COUNT(*) as total

        FROM dosen

        WHERE deleted_at IS NULL

        GROUP BY program_studi
    ";

    $stmt = $this->db->prepare($query);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function statusStats()
{
    $query = "
        SELECT
            status,
            COUNT(*) as total

        FROM dosen

        WHERE deleted_at IS NULL

        GROUP BY status
    ";

    $stmt = $this->db->prepare($query);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function totalSKS()
{
    $query = "
        SELECT
            SUM(mata_kuliah.sks) as total_sks

        FROM dosen_matakuliah

        LEFT JOIN mata_kuliah
            ON mata_kuliah.id = dosen_matakuliah.matakuliah_id
    ";

    $stmt = $this->db->prepare($query);

    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function exportData(
    $search = '',
    $programStudi = '',
    $status = ''
)
{
    $query = "
        SELECT *
        FROM dosen

        WHERE deleted_at IS NULL

        AND (
            nama LIKE :search1
            OR nidn LIKE :search2
        )
    ";

    if ($programStudi !== '') {

        $query .= "
            AND program_studi = :program_studi
        ";
    }

    if ($status !== '') {

        $query .= "
            AND status = :status
        ";
    }

    $query .= "
        ORDER BY id ASC
    ";

    $stmt = $this->db->prepare($query);

    $searchTerm = "%$search%";

    $stmt->bindValue(':search1', $searchTerm);

    $stmt->bindValue(':search2', $searchTerm);

    if ($programStudi !== '') {

        $stmt->bindValue(
            ':program_studi',
            $programStudi
        );
    }

    if ($status !== '') {

        $stmt->bindValue(
            ':status',
            $status
        );
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function logActivity(
    $userId,
    $aksi,
    $entitas,
    $entitasId = null,
    $keterangan = null
)
{
    $query = "
        INSERT INTO activity_log
        (
            user_id,
            aksi,
            entitas,
            entitas_id,
            keterangan
        )

        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $this->db->prepare($query);

    return $stmt->execute([
        $userId,
        $aksi,
        $entitas,
        $entitasId,
        $keterangan
    ]);
}

}