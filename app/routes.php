<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    //get
    $app->get('/produk', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM produk');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/detailpemesanan', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM detail_pemesanan');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/customer', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM customer');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/kategoriproduk', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM kategori_produk');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader('Content-Type', 'application/json');
    });
    

    //get by id
    $app->get('/produk/{Id_Produk}', function(Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM produk WHERE Id_Produk = ?');
        $query->execute([$args['Id_Produk']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            $response->getBody()->write(json_encode($results[0]));
        } else {
            $response->getBody()->write(json_encode(['message' => 'Data Produk tidak ditemukan']));
        }

        return $response->withHeader('Content-Type', 'application/json');
    });
    
    $app->get('/customer/{Id_Customer}', function(Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM customer WHERE Id_Customer = ?');
        $query->execute([$args['Id_Customer']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            $response->getBody()->write(json_encode($results[0]));
        } else {
            $response->getBody()->write(json_encode(['message' => 'Data Customer tidak ditemukan']));
        }

        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/detailpemesanan/{Id_Detail_Pemesanan}', function(Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM Detail_Pemesanan WHERE Id_Detail_Pemesanan = ?');
        $query->execute([$args['Id_Detail_Pemesanan']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            $response->getBody()->write(json_encode($results[0]));
        } else {
            $response->getBody()->write(json_encode(['message' => 'Data Detail_Pemesanan tidak ditemukan']));
        }

        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/kategoriproduk/{Id_Kategori}', function(Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM kategori_produk WHERE Id_Kategori = ?');
        $query->execute([$args['Id_Kategori']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            $response->getBody()->write(json_encode($results[0]));
        } else {
            $response->getBody()->write(json_encode(['message' => 'Data Kategori Produk tidak ditemukan']));
        }

        return $response->withHeader('Content-Type', 'application/json');
    });

    //post data
        $app->post('/produkinsert', function($request, $response, $args) {
    $parsedBody = $request->getParsedBody();

    $id = $parsedBody['Id_Produk'];
    $Nama = $parsedBody['Nama'];
    $Harga = $parsedBody['Harga'];
    $Stok = $parsedBody['Stok'];
    

    $db = $this->get(PDO::class);
    
    try {
        $query = $db->prepare('CALL InsertProduk(?, ?, ?, ?)');
        $query->execute([$id, $Nama, $Stok, $Harga]);

        $response->getBody()->write(json_encode([
            'message' => 'Produk Berhasil Ditambahkan Pada Id ' . $id
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Terjadi kesalahan dalam menambahkan produk: ' 
        ]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }   
    });

    $app->post('/kategoriprodukinsert', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
    
        $idkategori = $parsedBody['k_Id_Kategori'];
        $Nama = $parsedBody['k_Nama'];
        $idproduk = $parsedBody['k_Id_Produk'];
        $Statement = $parsedBody['Statement'];
    
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL UpdateDeleteKategoriProduk(?, ?, ?, ?)');
            $query->execute([$idkategori, $Nama, $idproduk, $Statement]);
    
            $response->getBody()->write(json_encode([
                'message' => 'Kategori Produk Berhasil Ditambahkan Pada Id ' . $idkategori
            ]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan dalam menambahkan kategori produk: ' 
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->post('/customerinsert', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
    
        $IdCustomer = $parsedBody['c_Id_Customer'];
        $Nama = $parsedBody['c_Nama'];
        $Alamat = $parsedBody['c_Alamat'];
        $Statement = $parsedBody['Statement'];
    
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL UpdateDeleteCustomer(?, ?, ?, ?)');
            $query->execute([$IdCustomer, $Nama, $Alamat, $Statement]);
    
            $response->getBody()->write(json_encode([
                'message' => 'Customer Berhasil Ditambahkan Pada Id ' . $IdCustomer
            ]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan dalam menambahkan Customer: ' 
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->post('/detailpemesananinsert', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
    
        $IdDetailPemesanan = $parsedBody['d_Id_Detail_Pemesanan'];
        $IdCustomer = $parsedBody['d_Id_Customer'];
        $Stokbeli = $parsedBody['d_StokBeli'];
        $IdProduk = $parsedBody['d_Id_Produk'];
        $Statement = $parsedBody['Statement'];
    
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL UpdateDeleteDetailPemesanan(?, ?, ?, ? ,?)');
            $query->execute([$IdDetailPemesanan, $IdCustomer, $Stokbeli, $IdProduk, $Statement]);
    
            $response->getBody()->write(json_encode([
                'message' => 'Detail Pemesanan Berhasil Ditambahkan Pada Id ' . $IdDetailPemesanan
            ]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan dalam menambahkan Detail Pemesanan: ' 
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });


    //put data
    $app->put('/produkupdate', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
        
        $id = $parsedBody['p_Id_Produk'];
        $Nama = $parsedBody['p_NamaBaru'];
        $Harga = $parsedBody['p_HargaBaru'];
        $Stok = $parsedBody['p_StokBaru'];
        $Statement = $parsedBody['p_Statement'];
        
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL UpdateDeleteProduk(?, ?, ?, ?, ?)');
            $query->execute([$id, $Nama, $Harga, $Stok, $Statement]);
        
            $affectedRows = $query->rowCount(); 
            if ($affectedRows > 0) {
                $response->getBody()->write(json_encode([
                    'message' => 'Produk Berhasil Diupdate Pada Id ' . $id
                ]));
            } else {
                $response->getBody()->write(json_encode([
                    'message' => 'Produk tidak ditemukan atau tidak ada perubahan dilakukan.'
                ]));
            }
        
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan dalam mengupdate kategori produk: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/kategoriprodukupdate', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
    
        $idkategori = $parsedBody['k_Id_Kategori'];
        $Nama = $parsedBody['k_Nama'];
        $idproduk = $parsedBody['k_Id_Produk'];
        $Statement = $parsedBody['Statement'];
    
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL UpdateDeleteKategoriProduk(?, ?, ?, ?)');
            $query->execute([$idkategori, $Nama, $idproduk, $Statement]);
    
            $response->getBody()->write(json_encode([
                'message' => 'Kategori Produk Berhasil Diupdate Pada Id ' . $idkategori
            ]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan dalam mengupdate kategori produk: ' 
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/customerupdate', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
    
        $IdCustomer = $parsedBody['c_Id_Customer'];
        $Nama = $parsedBody['c_Nama'];
        $Alamat = $parsedBody['c_Alamat'];
        $Statement = $parsedBody['Statement'];
    
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL UpdateDeleteCustomer(?, ?, ?, ?)');
            $query->execute([$IdCustomer, $Nama, $Alamat, $Statement]);
    
            $response->getBody()->write(json_encode([
                'message' => 'Customer Berhasil Diupdate Pada Id ' . $IdCustomer
            ]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan dalam mengupdate Customer: ' 
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->put('/detailpemesananupdate', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
    
        $IdDetailPemesanan = $parsedBody['d_Id_Detail_Pemesanan'];
        $IdCustomer = $parsedBody['d_Id_Customer'];
        $Stokbeli = $parsedBody['d_StokBeli'];
        $IdProduk = $parsedBody['d_Id_Produk'];
        $Statement = $parsedBody['Statement'];
    
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL UpdateDeleteDetailPemesanan(?, ?, ?, ? ,?)');
            $query->execute([$IdDetailPemesanan, $IdCustomer, $Stokbeli, $IdProduk, $Statement]);
    
            $response->getBody()->write(json_encode([
                'message' => 'Detail Pemesanan Berhasil Diupdate Pada Id ' . $IdDetailPemesanan
            ]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan dalam mengupdate Detail Pemesanan: ' 
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });
    
    //delete data
    $app->delete('/produkdelete', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
        
        $id = $parsedBody['p_Id_Produk'];
        $Nama = $parsedBody['p_NamaBaru'];
        $Stok = $parsedBody['p_StokBaru'];
        $Harga = $parsedBody['p_HargaBaru'];
        $Statement = $parsedBody['p_Statement'];
        
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL UpdateDeleteProduk(?, ?, ?, ?, ?)');
            $query->execute([$id, $Nama, $Stok, $Harga, $Statement]);

            $response->getBody()->write(json_encode([
                'message' => 'Produk Berhasil Dihapus Pada Id ' . $id
        
            ]));
        
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan dalam menghapus produk: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->delete('/kategoriprodukdelete', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
    
        $idkategori = $parsedBody['k_Id_Kategori'];
        $Nama = $parsedBody['k_Nama'];
        $idproduk = $parsedBody['k_Id_Produk'];
        $Statement = $parsedBody['Statement'];
    
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL UpdateDeleteKategoriProduk(?, ?, ?, ?)');
            $query->execute([$idkategori, $Nama, $idproduk, $Statement]);
    
            $response->getBody()->write(json_encode([
                'message' => 'Kategori Produk Berhasil Dihapus Pada Id ' . $idkategori
            ]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan dalam menghapus kategori produk: ' 
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->delete('/customerdelete', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
    
        $IdCustomer = $parsedBody['c_Id_Customer'];
        $Nama = $parsedBody['c_Nama'];
        $Alamat = $parsedBody['c_Alamat'];
        $Statement = $parsedBody['Statement'];
    
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL UpdateDeleteCustomer(?, ?, ?, ?)');
            $query->execute([$IdCustomer, $Nama, $Alamat, $Statement]);
    
            $response->getBody()->write(json_encode([
                'message' => 'Customer Berhasil Dihapuskan Pada Id ' . $IdCustomer
            ]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan dalam menghapus Customer: ' 
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

};

