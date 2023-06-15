<?php

namespace App\Test\Controller;

use App\Entity\Person;
use App\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PersonControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PersonRepository $repository;
    private string $path = '/person/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Person::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Person index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'person[email]' => 'Testing',
            'person[roles]' => 'Testing',
            'person[password]' => 'Testing',
            'person[first_name]' => 'Testing',
            'person[preprovision]' => 'Testing',
            'person[last_name]' => 'Testing',
            'person[date_of_birth]' => 'Testing',
            'person[hiring_date]' => 'Testing',
            'person[salary]' => 'Testing',
            'person[social_sec_number]' => 'Testing',
            'person[street]' => 'Testing',
            'person[place]' => 'Testing',
        ]);

        self::assertResponseRedirects('/person/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Person();
        $fixture->setEmail('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setFirst_name('My Title');
        $fixture->setPreprovision('My Title');
        $fixture->setLast_name('My Title');
        $fixture->setDate_of_birth('My Title');
        $fixture->setHiring_date('My Title');
        $fixture->setSalary('My Title');
        $fixture->setSocial_sec_number('My Title');
        $fixture->setStreet('My Title');
        $fixture->setPlace('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Person');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Person();
        $fixture->setEmail('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setFirst_name('My Title');
        $fixture->setPreprovision('My Title');
        $fixture->setLast_name('My Title');
        $fixture->setDate_of_birth('My Title');
        $fixture->setHiring_date('My Title');
        $fixture->setSalary('My Title');
        $fixture->setSocial_sec_number('My Title');
        $fixture->setStreet('My Title');
        $fixture->setPlace('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'person[email]' => 'Something New',
            'person[roles]' => 'Something New',
            'person[password]' => 'Something New',
            'person[first_name]' => 'Something New',
            'person[preprovision]' => 'Something New',
            'person[last_name]' => 'Something New',
            'person[date_of_birth]' => 'Something New',
            'person[hiring_date]' => 'Something New',
            'person[salary]' => 'Something New',
            'person[social_sec_number]' => 'Something New',
            'person[street]' => 'Something New',
            'person[place]' => 'Something New',
        ]);

        self::assertResponseRedirects('/person/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getRoles());
        self::assertSame('Something New', $fixture[0]->getPassword());
        self::assertSame('Something New', $fixture[0]->getFirst_name());
        self::assertSame('Something New', $fixture[0]->getPreprovision());
        self::assertSame('Something New', $fixture[0]->getLast_name());
        self::assertSame('Something New', $fixture[0]->getDate_of_birth());
        self::assertSame('Something New', $fixture[0]->getHiring_date());
        self::assertSame('Something New', $fixture[0]->getSalary());
        self::assertSame('Something New', $fixture[0]->getSocial_sec_number());
        self::assertSame('Something New', $fixture[0]->getStreet());
        self::assertSame('Something New', $fixture[0]->getPlace());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Person();
        $fixture->setEmail('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setFirst_name('My Title');
        $fixture->setPreprovision('My Title');
        $fixture->setLast_name('My Title');
        $fixture->setDate_of_birth('My Title');
        $fixture->setHiring_date('My Title');
        $fixture->setSalary('My Title');
        $fixture->setSocial_sec_number('My Title');
        $fixture->setStreet('My Title');
        $fixture->setPlace('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/person/');
    }
}
