<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    // Database configuration
    $dsn = 'mysql:host=localhost;dbname=rumah_sakit;charset=utf8mb4';
    $username = 'root';
    $password = '';

    // Set up PDO connection
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }



    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

 //FARMASIGROUP
 //OBAT
    $app->get('/obat', function (Request $request, Response $response) use ($pdo) {

        $stmt = $pdo->query('SELECT * FROM obat');
        $obats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($obats));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    $app->post("/obat", function (Request $request, Response $response) use ($pdo){

        $data = $request->getParsedBody();

        $stmt = $pdo->prepare('INSERT INTO obat (id_rm,sku, label_catatan, jumlah) VALUE (:id_rm,:sku, :label_catatan, :jumlah)');

        $data = [
            ":id_rm" => $data["id_rm"],
            ":sku" => $data["sku"],
            ":label_catatan" => $data["label_catatan"],
            ":jumlah" => $data["jumlah"]
        ];

        if($stmt->execute($data))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });

    // Get data by SKU untuk menampilkan data yang akan diupdate
    $app->get("/obat/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];

        $stmt = $pdo->prepare('SELECT * FROM obat WHERE sku = :sku');
        $stmt->execute([':sku' => $sku]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if($data)
        {
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'not_found']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    });


    // Update data
    $app->put("/obat/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];
        $requestData = $request->getParsedBody();

        $stmt = $pdo->prepare('UPDATE obat SET id_rm = :id_rm, label_catatan = :label_catatan, jumlah = :jumlah WHERE sku = :sku');

        $data = [
            ":sku" => $sku,
            ":id_rm" => $requestData["id_rm"],
            ":label_catatan" => $requestData["label_catatan"],
            ":jumlah" => $requestData["jumlah"]
        ];


        if($stmt->execute($data))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });


    // Delete data
    $app->delete("/obat/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];

        $stmt = $pdo->prepare('DELETE FROM obat WHERE sku = :sku');

        if($stmt->execute([':sku' => $sku]))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });

    // Get data by SKU untuk menampilkan data yang akan diupdate
    $app->get("/farmasi/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];

        $stmt = $pdo->prepare('SELECT * FROM farmasi WHERE sku = :sku');
        $stmt->execute([':sku' => $sku]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if($data)
        {
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'not_found']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    });

    // Update data
    $app->put("/farmasi/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];
        $requestData = $request->getParsedBody();

        $stmt = $pdo->prepare('UPDATE farmasi SET label = :label, dosis = :dosis WHERE sku = :sku');

        $data = [
            ":sku" => $sku,
            ":label" => $requestData["label"],
            ":dosis" => $requestData["dosis"]
        ];

        if($stmt->execute($data))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });

    // Delete data
    $app->delete("/farmasi/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['sku'];

        $stmt = $pdo->prepare('DELETE FROM farmasi WHERE sku = :sku');

        if($stmt->execute([':sku' => $sku]))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });


//END OBAT
//DATA FARMASI
    
    $app->get('/farmasi', function (Request $request, Response $response) use ($pdo) {
        $stmt = $pdo->query('SELECT * FROM farmasi');
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($items));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    //insert
    $app->post("/farmasi", function (Request $request, Response $response) use ($pdo){
        $data = $request->getParsedBody();
        
        $stmt = $pdo->prepare('INSERT INTO farmasi (nama_obat, sku, dosis, label) VALUES (:nama_obat, :sku, :dosis, :label)');
        
        $data = [
            ":nama_obat" => $data["nama_obat"],
            ":sku" => $data["sku"],
            ":dosis" => $data["dosis"],
            ":label" => $data["label"]
        ];
        
        if($stmt->execute($data)) {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });
    


//END DATA FARMASI
//END FARMASIGROUP



    $app->post("/pasien", function (Request $request, Response $response) use ($pdo){

        $data = $request->getParsedBody(); 

        $stmt = $pdo->prepare('INSERT INTO pasien (alamat,berat,gol_darah,jk,kontak_keluarga,kontak_keluarga_alamat,
                                kontak_keluarga_hp,nama,nik,no_hp, no_rm,tempat_lahir,tgl_lahir,tinggi) 
                                VALUE (:alamat,:berat,:gol_darah,:jk,:kontak_keluarga,:kontak_keluarga_alamat,
                                :kontak_keluarga_hp,:nama,:nik,:no_hp,: no_rm,:tempat_lahir,:tgl_lahir,:tinggi)');

        $data = [
            ":alamat"  => $data["alamat"],
            ":berat"  => $data["berat"],
            ":gol_darah"  => $data["gol_darah"],
            ":jk"  => $data["jk"],
            ":kontak_keluarga"  => $data["kontak_keluarga"],
            ":kontak_keluarga_alamat"  => $data["kontak_keluarga_alamat"],
            ":kontak_keluarga_hp"  => $data["kontak_keluarga_hp"],
            ":nama"  => $data["nama"],
            ":nik " => $data["nik"],
            ":no_hp"  => $data["no_hp"],
            ":no_rm"  => $data["no_rm "],
            ":tempat_lahir"  => $data["tempat_lahir"],
            ":tgl_lahir"  => $data["tgl_lahir "],
            ":tinggi"  => $data["tinggi "]
        ];
    
        if($stmt->execute($data))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });

    // Get data by nama untuk menampilkan data 
    $app->get("/pasien/{nama}", function (Request $request, Response $response, $args) use ($pdo){
        $nama = $args['nama'];

        $stmt = $pdo->prepare('SELECT * FROM pasien WHERE nama = :nama');
        $stmt->execute([':nama' => $nama]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if($data)
        {
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'not_found']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    });


    // Update data
    $app->put("/update_pasien/{nama}", function (Request $request, Response $response, $args) use ($pdo){
        $sku = $args['nama'];
        $requestData = $request->getParsedBody();

        $stmt = $pdo->prepare('UPDATE pasien SET nik = :nik, berat = :berat WHERE nama = :nama');

        $data = [
            ":alamat"  => $requestData["alamat"],
            ":berat"  => $requestData["berat"],
            ":gol_darah"  => $requestData["gol_darah"],
            ":jk"  => $requestData["jk"],
            ":kontak_keluarga"  => $requestData["kontak_keluarga"],
            ":kontak_keluarga_alamat"  => $requestData["kontak_keluarga_alamat"],
            ":kontak_keluarga_hp"  => $requestData["kontak_keluarga_hp"],
            ":nama"  => $requestData["nama"],
            ":nik " => $requestData["nik"],
            ":no_hp"  => $requestData["no_hp"],
            ":no_rm"  => $requestData["no_rm "],
            ":tempat_lahir"  => $requestData["tempat_lahir"],
            ":tgl_lahir"  => $requestData["tgl_lahir "],
            ":tinggi"  => $requestData["tinggi "]
        ];

        if($stmt->execute($data))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });


    // Delete data
    $app->delete("/delete_pasien/{nama}", function (Request $request, Response $response, $args) use ($pdo){
        $nama = $args['nama'];

        $stmt = $pdo->prepare('DELETE FROM pasien WHERE nama = :nama');

        if($stmt->execute([':nama' => $nama]))
        {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });
        
    //  END Pasien
    // Mulai Rekam Medis
    $app->group('/rekam-medis', function (Group $group) use ($pdo) {

        // Mendapatkan semua data rekam medis
        $group->get('', function (Request $request, Response $response) use ($pdo) {
            $stmt = $pdo->query('SELECT rekam_medis.no_rm AS no_rm,  pasien.nama AS nama_pasien, tindakan.deskripsi AS deskripsi_tindakan, farmasi.nama_obat AS nama_obat
                                 FROM rekam_medis
                                 INNER JOIN pasien ON rekam_medis.no_rm = pasien.no_rm
                                 INNER JOIN tindakan ON rekam_medis.no_rm = tindakan.no_rm
                                 INNER JOIN obat ON rekam_medis.sku = obat.sku
                                 INNER JOIN farmasi ON obat.sku = farmasi.sku');
            $rekam_medis = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response->getBody()->write(json_encode($rekam_medis));
            return $response->withHeader('Content-Type', 'application/json');
        });
        // Mendapatkan data lengkap pasien berdasarkan nomor rekam medis
    });

    $app->group('/detail-pasien', function (Group $group) use ($pdo) {
        // Mendapatkan data lengkap pasien berdasarkan nomor rekam medis
        $group->get('/{no_rm}', function (Request $request, Response $response, array $args) use ($pdo) {
            $no_rm = $args['no_rm'];
            $stmt = $pdo->prepare('SELECT * FROM pasien WHERE no_rm = :no_rm');
            $stmt->execute([':no_rm' => $no_rm]);
            $pasien = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$pasien) {
                $response->getBody()->write(json_encode(['error' => 'Data pasien tidak ditemukan']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            $response->getBody()->write(json_encode($pasien));
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $app->group('/riwayat', function (Group $group) use ($pdo) {
    // Mendapatkan riwayat pasien berdasarkan nomor rekam medis
    $group->get('/{no_rm}', function (Request $request, Response $response, array $args) use ($pdo) {
        $no_rm = $args['no_rm'];
        // Mendapatkan riwayat pasien berdasarkan nomor rekam medis dari tabel rekam_medis
        $stmt_riwayat = $pdo->prepare('SELECT id_rm, tanggal, keluhan, tinggi, berat, tensi, dokter FROM rekam_medis WHERE no_rm = :no_rm');
        $stmt_riwayat->execute([':no_rm' => $no_rm]);
        $riwayat_pasien = $stmt_riwayat->fetchAll(PDO::FETCH_ASSOC);

        if (empty($riwayat_pasien)) {
            $response->getBody()->write(json_encode(['error' => 'Riwayat pasien tidak ditemukan']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        // Ambil data obat berdasarkan id_rm
        foreach ($riwayat_pasien as &$riwayat) {
            $id_rm = $riwayat['id_rm'];
            $stmt_obat = $pdo->prepare('SELECT sku FROM obat WHERE id_rm = :id_rm');
            $stmt_obat->execute([':id_rm' => $id_rm]);
            $sku_results = $stmt_obat->fetchAll(PDO::FETCH_ASSOC);

            $nama_obat = [];
            foreach ($sku_results as $sku_row) {
                $sku = $sku_row['sku'];
                // Ambil nama obat dari tabel farmasi berdasarkan sku
                $stmt_nama_obat = $pdo->prepare('SELECT nama_obat FROM farmasi WHERE sku = :sku');
                $stmt_nama_obat->execute([':sku' => $sku]);
                $nama_obat_result = $stmt_nama_obat->fetch(PDO::FETCH_ASSOC);
                if ($nama_obat_result) {
                    $nama_obat[] = $nama_obat_result['nama_obat'];
                }
            }
            $riwayat['obat'] = $nama_obat;
        }

        $response->getBody()->write(json_encode($riwayat_pasien));
        return $response->withHeader('Content-Type', 'application/json');
    });
});

$app->group('/cari_pasien', function (Group $group) use ($pdo) {
    // Mendapatkan data lengkap pasien berdasarkan nomor rekam medis
    $group->get('/{no_rm}', function (Request $request, Response $response, array $args) use ($pdo) {
        $no_rm = $args['no_rm'];

        // Prepare statement
        $stmt = $pdo->prepare('SELECT no_rm, nama, jk, gol_darah, tinggi, berat FROM pasien WHERE no_rm = :no_rm');
        $stmt->execute([':no_rm' => $no_rm]);

        // Fetch data
        $pasien = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika data tidak ditemukan, kirim response error
        if (!$pasien) {
            $response->getBody()->write(json_encode(['error' => 'Data pasien tidak ditemukan']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        // Kirim data pasien dalam format JSON
        $response->getBody()->write(json_encode($pasien));
        return $response->withHeader('Content-Type', 'application/json');
    });
});

  // API OBAT
  $app->group('/cari_obat', function (Group $group) use ($pdo) {
    // Mendapatkan data lengkap obat berdasarkan ID
    $group->get('/{id}', function (Request $request, Response $response, array $args) use ($pdo) {
        $id = $args['id'];

        // Prepare statement
        $stmt = $pdo->prepare('SELECT * FROM farmasi WHERE id = :id');
        $stmt->execute([':id' => $id]);

        // Fetch data
        $obat = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika data tidak ditemukan, kirim response error
        if (!$obat) {
            $response->getBody()->write(json_encode(['error' => 'Data obat tidak ditemukan']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        // Kirim data obat dalam format JSON
        $response->getBody()->write(json_encode($obat));
        return $response->withHeader('Content-Type', 'application/json');
    });
});

    //TAMPIL STATUS OBAT
            $app->get('/tampil_all', function (Request $request, Response $response) use ($pdo) {

                $stmt = $pdo->query('SELECT id_rm, no_rm, keluhan, tinggi, berat, tensi, dokter, status_obat, sku, tanggal
                FROM rekam_medis
                WHERE status_obat IN ("menunggu", "diproses")
                AND tanggal = CURDATE();
                ');
                $status = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $response->getBody()->write(json_encode($status));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            });
            
            $app->get('/tampil_tunggu', function (Request $request, Response $response) use ($pdo) {

                $stmt = $pdo->query('SELECT id_rm, no_rm, keluhan, tinggi, berat, tensi, dokter, status_obat, sku, tanggal
                FROM rekam_medis
                WHERE status_obat = "menunggu"
                AND tanggal = CURDATE();
                ');
                $status = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $response->getBody()->write(json_encode($status));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            });

            $app->get('/tampil_proses', function (Request $request, Response $response) use ($pdo) {

                $stmt = $pdo->query('SELECT id_rm, no_rm, keluhan, tinggi, berat, tensi, dokter, status_obat, sku, tanggal
                FROM rekam_medis
                WHERE status_obat = "diproses"
                AND tanggal = CURDATE();
                ');
                $status = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $response->getBody()->write(json_encode($status));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            });

            $app->put('/proses_rm/{id}', function (Request $request, Response $response, array $args) use ($pdo) {
                $id = $args['id'];
            
                $stmt = $pdo->prepare('UPDATE rekam_medis SET status_obat = "diproses" WHERE id_rm = :id');
                $result = $stmt->execute([':id' => $id]);
            
                if ($result) {
                    $response->getBody()->write(json_encode(['status' => 'berhasil']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
                } else {
                    $response->getBody()->write(json_encode(['status' => 'failed']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                }
            });

            $app->put('/selesai_rm/{id}', function (Request $request, Response $response, array $args) use ($pdo) {
                $id = $args['id'];
            
                $stmt = $pdo->prepare('UPDATE rekam_medis SET status_obat = "selesai" WHERE id_rm = :id');
                $result = $stmt->execute([':id' => $id]);
            
                if ($result) {
                    $response->getBody()->write(json_encode(['status' => 'berhasil']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
                } else {
                    $response->getBody()->write(json_encode(['status' => 'failed']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                }
            });
            //RAWAT JALAN
    // Menampilkan Seluruh data setelah dientri ke rekam medis Sort By TODAY
    $app->get('/rawatjalan', function(Request $request, Response $response) use ($pdo) {
        // $response-> getBody()->write(json_encode(['foo'=>'bar']));
        $stmt = $pdo->query('SELECT 
            tindakan.id AS id,
            rekam_medis.id_rm AS id_rm,  
            rekam_medis.no_rm AS no_rm,
            pasien.nama AS nama, 
            tindakan.deskripsi AS deskripsi, 
            farmasi.sku AS sku,
            farmasi.nama_obat AS nama_obat,
            obat.label_catatan AS label_catatan,
            obat.jumlah AS jumlah
        FROM rekam_medis
        LEFT JOIN pasien ON rekam_medis.no_rm = pasien.no_rm
        LEFT JOIN tindakan ON rekam_medis.no_rm = tindakan.no_rm
        LEFT JOIN obat ON rekam_medis.id_rm = obat.id_rm
        LEFT JOIN farmasi ON obat.sku = farmasi.sku
        WHERE DATE(rekam_medis.tanggal) = CURDATE()
        ');
    // WHERE DATE(rekam_medis.tanggal) = CURDATE()
        $obats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($obats));

        return $response->withHeader('Content-Type','application/json')->withStatus(201);
    });

    // ------------- GET data REKAM MEDIS by:id_rm----------------
    $app->get("/rawatjalan/rekammedis/{id_rm}", function (Request $request, Response $response, $args) use ($pdo){
        $id_rm = $args['id_rm'];
        $stmt = $pdo->prepare('SELECT 
            *
        FROM rekam_medis  
        WHERE id_rm = :id_rm');
        $stmt->execute([':id_rm' => $id_rm]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if($data)
        {
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });

    // ------------- GET data OBAT by:id,id_rm,sku----------------
    $app->get("/rawatjalan/obat/{id}/{id_rm}/{sku}", function (Request $request, Response $response, $args) use ($pdo){
        $id_rm = $args['id_rm'];
        $id = $args['id'];
        $sku = $args['sku'];
        $stmt = $pdo->prepare('SELECT 
            obat.id AS id, 
            obat.id_rm AS id_rm,  
            obat.sku AS sku,
            farmasi.nama_obat AS nama_obat,
            obat.label_catatan AS label_catatan,
            obat.jumlah AS jumlah
        FROM obat JOIN farmasi ON obat.sku = farmasi.sku 
        WHERE obat.id_rm = :id_rm AND obat.sku = :sku');
        $stmt->execute([':id_rm' => $id_rm, ':sku' => $sku]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if($data)
        {
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        // TAMBAH DATA SECARA OTOMATIS DAN DI SET NULL
        // Menyiapkan dan menjalankan query untuk mendapatkan no_rm dari tabel rekam_medis
        $id_rm = $args['id_rm'];
        $stmt = $pdo->prepare('SELECT id_rm FROM rekam_medis WHERE id_rm = :id_rm');
        $stmt->execute([':id_rm' => $id_rm]);
        $rekamMedis = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rekamMedis) {
            $id_rm = $rekamMedis['id_rm'];
        } else {
            $id_rm = "0"; // Default value jika tidak ditemukan
        }

        // Data yang akan diinsert ke dalam tabel obat
        $postData = [
            "id_rm" => $id_rm,
            "sku" => "NULL", // Atau nilai lainnya yang relevan
            "label_catatan" => "NULL",
            "jumlah" => "NULL"
        ];

        // Menyiapkan dan menjalankan query untuk menyimpan data baru ke dalam tabel obat
        $stmt = $pdo->prepare('INSERT INTO obat (id_rm, sku, label_catatan, jumlah) VALUES ( :id_rm, :sku, :label_catatan, :jumlah)');
        $postResult = $stmt->execute([
            ':id_rm' => $postData['id_rm'],
            ':sku' => $postData['sku'],
            ':label_catatan' => $postData['label_catatan'],
            ':jumlah' => $postData['jumlah']
        ]);

        // Jika operasi POST berhasil, kirimkan respons sukses
        if ($postResult) {
            $response->getBody()->write(json_encode(['status' => 'post_success']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }

        // Jika operasi POST gagal, kirimkan respons gagal
        $response->getBody()->write(json_encode(['status' => 'post_failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });
    // ------------- UPDATE data OBAT by:id,id_rm----------------
    $app->put("/rawatjalan/obat/{id}/{id_rm}", function (Request $request, Response $response, $args) use ($pdo) {
        $id = $args['id'];
        $id_rm = $args['id_rm'];
        $requestData = $request->getParsedBody();
    
        // Menyiapkan query update
        $stmt = $pdo->prepare(
            'UPDATE obat 
            SET sku = :sku, label_catatan = :label_catatan, jumlah = :jumlah 
            WHERE id = :id AND id_rm = :id_rm');
    
        // Data untuk query
        $data = [
            ":id" => $id,
            ":id_rm" => $id_rm,
            ":sku" => $requestData["sku"],
            ":label_catatan" => $requestData["label_catatan"],
            ":jumlah" => $requestData["jumlah"]
        ];
    
        try {
            // Eksekusi query
            if ($stmt->execute($data)) {
                $response->getBody()->write(json_encode(['status' => 'berhasil']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['status' => 'update_failed']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            // Menangani kesalahan eksekusi query
            $response->getBody()->write(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });
        
    // ------------- GET data TINDAKAN by:id,id_rm----------------
    $app->get("/rawatjalan/Tindakan/{id}/{id_rm}", function (Request $request, Response $response, $args) use ($pdo){
        $id = $args['id'];

        $stmt = $pdo->prepare('SELECT * FROM tindakan WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if($data)
        {
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        // Menyiapkan dan menjalankan query untuk mendapatkan no_rm dari tabel rekam_medis
        $id_rm = $args['id_rm'];
        $stmt = $pdo->prepare('SELECT no_rm FROM rekam_medis WHERE id_rm = :id_rm');
        $stmt->execute([':id_rm' => $id_rm]);
        $rekamMedis = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rekamMedis) {
            $no_rm = $rekamMedis['no_rm'];
        } else {
            $no_rm = "0"; // Default value jika tidak ditemukan
        }

        // Data yang akan diinsert ke dalam tabel tindakan
        $postData = [
            "id_rm" => $id_rm,
            "no_rm" => $no_rm,
            "deskripsi" => "NULL" // Atau nilai lainnya yang relevan
        ];

        // Menyiapkan dan menjalankan query untuk menyimpan data baru ke dalam tabel tindakan
        $stmt = $pdo->prepare('INSERT INTO tindakan (id_rm, no_rm, deskripsi) VALUES (:id_rm, :no_rm, :deskripsi)');
        $postResult = $stmt->execute([
            ':id_rm' => $postData['id_rm'],
            ':no_rm' => $postData['no_rm'],
            ':deskripsi' => $postData['deskripsi']
        ]);

        // Jika operasi POST berhasil, kirimkan respons sukses
        if ($postResult) {
            $response->getBody()->write(json_encode(['status' => 'post_success']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }

        // Jika operasi POST gagal, kirimkan respons gagal
        $response->getBody()->write(json_encode(['status' => 'post_failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });

    // ------------- UPDATE data TINDAKAN by:id,id_rm----------------
    $app->put("/rawatjalan/Tindakan/{id}/{id_rm}", function (Request $request, Response $response, $args) use ($pdo) {
        $id_rm = $args['id_rm'];
        $id = $args['id'];
        $requestData = $request->getParsedBody();
      
        // Ensure "deskripsi" is present in request data
        if (!isset($requestData["deskripsi"])) {
          $response->getBody()->write(json_encode(['status' => 'error: missing_deskripsi']));
          return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // Bad request
        }
      
        $stmt = $pdo->prepare('UPDATE tindakan 
                                SET deskripsi = :deskripsi 
                                WHERE id = :id AND id_rm = :id_rm');
      
        $data = [
          ":id" => $id,  // Assuming 'id' is available through another mechanism
          ":id_rm" => $id_rm,
          ":deskripsi" => $requestData["deskripsi"]
        ];
      
        if ($stmt->execute($data)) {
          $response->getBody()->write(json_encode(['status' => 'berhasil']));
          return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
          
        }
      
        $response->getBody()->write(json_encode(['status' => 'failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
      });

    // ------------- DELETE data OBAT by:id,sku----------------
    $app->delete("/rawatjalan/delete/{id}/{sku}", function (Request $request, Response $response, $args) use ($pdo){
            $sku = $args['sku'];
            $id = $args['id'];

            $stmt = $pdo->prepare('DELETE FROM obat  WHERE id = :id AND sku = :sku');
    
            if($stmt->execute([':id' => $id, ':sku' => $sku]))
            {
                $response->getBody()->write(json_encode(['status' => 'berhasil']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
            
            $response->getBody()->write(json_encode(['status' => 'failed']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });
    // ------------- POST data OBAT by:id,id_rm,sku----------------

    $app->post("/rawatjalan/tambah/obat/{id}/{id_rm}", function (Request $request, Response $response, $args) use ($pdo){
        $id_rm = $args['id_rm'];
        $stmt = $pdo->prepare('SELECT id_rm FROM rekam_medis WHERE id_rm = :id_rm');
        $stmt->execute([':id_rm' => $id_rm]);
        $rekamMedis = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rekamMedis) {
            $id_rm = $rekamMedis['id_rm'];
        } else {
            $id_rm = "0"; // Default value jika tidak ditemukan
        }

        // Data yang akan diinsert ke dalam tabel obat
        $postData = [
            "id_rm" => $id_rm,
            "sku" => "NULL", // Atau nilai lainnya yang relevan
            "label_catatan" => "NULL",
            "jumlah" => "NULL"
        ];

        // Menyiapkan dan menjalankan query untuk menyimpan data baru ke dalam tabel obat
        $stmt = $pdo->prepare('INSERT INTO obat (id_rm, sku, label_catatan, jumlah) VALUES ( :id_rm, :sku, :label_catatan, :jumlah)');
        $postResult = $stmt->execute([
            ':id_rm' => $postData['id_rm'],
            ':sku' => $postData['sku'],
            ':label_catatan' => $postData['label_catatan'],
            ':jumlah' => $postData['jumlah']
        ]);

        // Jika operasi POST berhasil, kirimkan respons sukses
        if ($postResult) {
            $response->getBody()->write(json_encode(['status' => 'berhasil']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }

        // Jika operasi POST gagal, kirimkan respons gagal
        $response->getBody()->write(json_encode(['status' => 'gagal']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    });  
// END RAWAT JALAN
};