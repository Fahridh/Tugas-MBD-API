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
    

    //post data
        $app->post('/insert', function($request, $response, $args) {
    $parsedBody = $request->getParsedBody();

    $id = $parsedBody['Id_Produk'];
    $Nama = $parsedBody['Nama'];
    $Harga = $parsedBody['Harga'];
    $Stok = $parsedBody['Stok'];

    $db = $this->get(PDO::class);
    
    try {
        $query = $db->prepare('CALL InsertProduk(?, ?, ?, ?)');
        $query->execute([$id, $Nama, $Harga, $Stok]);

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

    //put data
    $app->put('/update', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
        
        $id = $parsedBody['p_Id_Produk'];
        $Nama = $parsedBody['p_NamaBaru'];
        $Harga = $parsedBody['p_HargaBaru'];
        $Stok = $parsedBody['p_StokBaru'];
        $Statement = $parsedBody['p_Statement'];
        
        $db = $this->get(PDO::class);
        
        try {
            // Menggunakan prepared statement untuk mencegah SQL injection
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
                'error' => 'Terjadi kesalahan dalam mengupdate produk: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });
    
    //delete data
    $app->delete('/delete', function($request, $response, $args) {
        $parsedBody = $request->getParsedBody();
        
        $id = $parsedBody['p_Id_Produk'];
        $Nama = $parsedBody['p_NamaBaru'];
        $Harga = $parsedBody['p_HargaBaru'];
        $Stok = $parsedBody['p_StokBaru'];
        $Statement = $parsedBody['p_Statement'];
        
        $db = $this->get(PDO::class);
        
        try {
            // Menggunakan prepared statement untuk mencegah SQL injection
            $query = $db->prepare('CALL UpdateDeleteProduk(?, ?, ?, ?, ?)');
            $query->execute([$id, $Nama, $Harga, $Stok, $Statement]);

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



};

