<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    /**
     * Test 1 : Accès à la page de liste
     */
    public function testListPageIsAccessible(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/liste');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste de contacts');
    }

    /**
     * Test 2 : Pagination - Page 1 existe
     */
    public function testPaginationPage1(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/liste/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.paginate');
    }

    /**
     * Test 3 : Pagination - Page 2 existe
     */
    public function testPaginationPage2(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/liste/2');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test 4 : Recherche vide retourne tous les contacts
     */
    public function testSearchWithEmptyQuery(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/liste?search=');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.paginate');
    }

    /**
     * Test 5 : Recherche avec un prénom
     */
    public function testSearchByFirstName(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/liste?search=Benoit');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test 6 : Recherche avec un nom
     */
    public function testSearchByLastName(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/liste?search=Dupont');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test 7 : Filtre par statut new
     */
    public function testFilterByStatusNew(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/liste?status=new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('select#status option[value="new"][selected]');
    }

    /**
     * Test 8 : Filtre par statut traité
     */
    public function testFilterByStatusTreated(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/liste?status=treated');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('select#status option[value="treated"][selected]');
    }


}
