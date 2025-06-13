<?php
namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class ProduitApiTest extends ApiTestCase
{
    public function testGetProduitsCollection(): void
    {
        static::createClient()->request('GET', '/api/produits');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetProduitItem(): void
    {
        $client = static::createClient();
        $response = $client->request('GET', '/api/produits');
        $data = $response->toArray();
        $id = $data['hydra:member'][0]['id'] ?? null;
        $this->assertNotNull($id, 'Aucun produit trouvé');
        $client->request('GET', '/api/produits/' . $id);
        $this->assertResponseStatusCodeSame(200);
    }
}
