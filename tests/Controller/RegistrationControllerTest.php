<?php

namespace App\Test\Controller;

use App\Entity\Registration;
use App\Repository\RegistrationRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private RegistrationRepository $repository;
    private string $path = '/registration/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Registration::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Registration index');

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
            'registration[payment]' => 'Testing',
            'registration[person]' => 'Testing',
            'registration[lesson]' => 'Testing',
        ]);

        self::assertResponseRedirects('/registration/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Registration();
        $fixture->setPayment('My Title');
        $fixture->setPerson('My Title');
        $fixture->setLesson('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Registration');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Registration();
        $fixture->setPayment('My Title');
        $fixture->setPerson('My Title');
        $fixture->setLesson('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'registration[payment]' => 'Something New',
            'registration[person]' => 'Something New',
            'registration[lesson]' => 'Something New',
        ]);

        self::assertResponseRedirects('/registration/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getPayment());
        self::assertSame('Something New', $fixture[0]->getPerson());
        self::assertSame('Something New', $fixture[0]->getLesson());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Registration();
        $fixture->setPayment('My Title');
        $fixture->setPerson('My Title');
        $fixture->setLesson('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/registration/');
    }
}
