<?php

namespace App\Tests\Service\Feeds;

use App\Document\Feed;
use App\Exception\FeedsImporterException;
use App\Repository\FeedRepository;
use App\Service\DailyHelper;
use App\Service\Feeds\FeedsHandler;
use App\Service\Feeds\FeedsImporter;
use App\Service\Newspaper\NewspaperFactory;
use App\Service\Newspaper\NewspaperInterface;
use DateTime;
use Doctrine\ODM\MongoDB\MongoDBException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FeedsHandlerTest extends TestCase
{
    private MockObject|FeedRepository $feedRepositoryMock;
    private MockObject|NewspaperFactory $newspaperFactoryMock;
    private MockObject|FeedsImporter $feedsImporterMock;
    private MockObject|DailyHelper $dailyHelperMock;

    private FeedsHandler $feedsHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->feedRepositoryMock = $this->createMock(FeedRepository::class);
        $this->newspaperFactoryMock = $this->createMock(NewspaperFactory::class);
        $this->feedsImporterMock = $this->createMock(FeedsImporter::class);
        $this->dailyHelperMock = $this->createMock(DailyHelper::class);

        $this->feedsHandler = new FeedsHandler(
            $this->feedRepositoryMock,
            $this->newspaperFactoryMock,
            $this->feedsImporterMock,
            $this->dailyHelperMock
        );
    }

    public function testOnlyValidKeysAreProcessed(): void
    {
        $inputNewspaperKeys = ['el_mundo', 'el_pais', 'invalid_key'];

        $newspaperMock1 = $this->createMock(NewspaperInterface::class);
        $newspaperMock2 = $this->createMock(NewspaperInterface::class);

        $this->newspaperFactoryMock
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                ['el_mundo', $newspaperMock1],
                ['el_pais', $newspaperMock2],
            ]);

        $this->feedsImporterMock
            ->expects($this->exactly(2))
            ->method('import')
            ->with($this->isInstanceOf(NewspaperInterface::class));

        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');

        $this->dailyHelperMock
            ->expects($this->once())
            ->method('today')
            ->willReturn($today);

        $this->dailyHelperMock
            ->expects($this->once())
            ->method('tomorrow')
            ->willReturn($tomorrow);

        $feed1 = new Feed();
        $feed1->setTitle('Feed 1')
            ->setNewspaperKey('el_mundo')
            ->setPublishedAt($today)
            ->setNewspaperName('El Mundo');

        $feed2 = new Feed();
        $feed2->setTitle('Feed 2')
            ->setNewspaperKey('el_pais')
            ->setPublishedAt($today)
            ->setNewspaperName('El País');

        $expectedFeeds = [$feed1, $feed2];

        $this->feedRepositoryMock
            ->expects($this->once())
            ->method('findNewspapersFeedsInRange')
            ->with(
                $this->equalTo(['el_mundo', 'el_pais', 'invalid_key']),
                $this->equalTo($today),
                $this->equalTo($tomorrow),
                $this->equalTo(15)
            )
            ->willReturn($expectedFeeds);

        $result = $this->feedsHandler->getTrendingFeeds($inputNewspaperKeys);

        $this->assertSame($expectedFeeds, $result);
    }

    public function testAllInvalidKeysAreIgnored(): void
    {
        $inputNewspaperKeys = ['invalid_key1', 'invalid_key2'];

        $this->newspaperFactoryMock
            ->expects($this->never())
            ->method('create');

        $this->feedsImporterMock
            ->expects($this->never())
            ->method('import');

        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');

        $this->dailyHelperMock
            ->expects($this->once())
            ->method('today')
            ->willReturn($today);

        $this->dailyHelperMock
            ->expects($this->once())
            ->method('tomorrow')
            ->willReturn($tomorrow);

        $expectedFeeds = [];

        $this->feedRepositoryMock
            ->expects($this->once())
            ->method('findNewspapersFeedsInRange')
            ->with(
                $this->equalTo(['invalid_key1', 'invalid_key2']),
                $this->equalTo($today),
                $this->equalTo($tomorrow),
                $this->equalTo(15)
            )
            ->willReturn($expectedFeeds);

        $result = $this->feedsHandler->getTrendingFeeds($inputNewspaperKeys);

        $this->assertSame($expectedFeeds, $result);
    }

    public function testMixOfValidAndInvalidKeys(): void
    {
        $inputNewspaperKeys = ['el_mundo', 'invalid_key1', 'el_pais', 'invalid_key2'];

        $newspaperMock1 = $this->createMock(NewspaperInterface::class);
        $newspaperMock2 = $this->createMock(NewspaperInterface::class);

        $this->newspaperFactoryMock
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                ['el_mundo', $newspaperMock1],
                ['el_pais', $newspaperMock2],
            ]);

        $this->feedsImporterMock
            ->expects($this->exactly(2))
            ->method('import')
            ->with($this->isInstanceOf(NewspaperInterface::class));

        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');

        $this->dailyHelperMock
            ->expects($this->once())
            ->method('today')
            ->willReturn($today);

        $this->dailyHelperMock
            ->expects($this->once())
            ->method('tomorrow')
            ->willReturn($tomorrow);

        $feed1 = new Feed();
        $feed1->setTitle('Feed 1')
            ->setNewspaperKey('el_mundo')
            ->setPublishedAt($today)
            ->setNewspaperName('El Mundo');

        $feed2 = new Feed();
        $feed2->setTitle('Feed 2')
            ->setNewspaperKey('el_pais')
            ->setPublishedAt($today)
            ->setNewspaperName('El País');

        $expectedFeeds = [$feed1, $feed2];

        $this->feedRepositoryMock
            ->expects($this->once())
            ->method('findNewspapersFeedsInRange')
            ->with(
                $this->equalTo(['el_mundo', 'invalid_key1', 'el_pais', 'invalid_key2']),
                $this->equalTo($today),
                $this->equalTo($tomorrow),
                $this->equalTo(15)
            )
            ->willReturn($expectedFeeds);

        $result = $this->feedsHandler->getTrendingFeeds($inputNewspaperKeys);

        $this->assertSame($expectedFeeds, $result);
    }

    public function testFeedsImporterThrowsException(): void
    {
        $this->expectException(FeedsImporterException::class);

        $inputNewspaperKeys = ['el_mundo', 'el_pais'];

        $newspaperMock1 = $this->createMock(NewspaperInterface::class);

        $this->newspaperFactoryMock
            ->expects($this->exactly(1))
            ->method('create')
            ->with('el_mundo')
            ->willReturn($newspaperMock1);

        $this->feedsImporterMock
            ->expects($this->exactly(1))
            ->method('import')
            ->with($newspaperMock1)
            ->willThrowException(new FeedsImporterException('Import failed'));

        $this->dailyHelperMock
            ->expects($this->never())
            ->method('today');

        $this->dailyHelperMock
            ->expects($this->never())
            ->method('tomorrow');

        $this->feedsHandler->getTrendingFeeds($inputNewspaperKeys);
    }

    public function testRepositoryThrowsException(): void
    {
        $this->expectException(MongoDBException::class);

        $inputNewspaperKeys = ['el_mundo', 'el_pais'];

        $newspaperMock1 = $this->createMock(NewspaperInterface::class);
        $newspaperMock2 = $this->createMock(NewspaperInterface::class);

        $this->newspaperFactoryMock
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                ['el_mundo', $newspaperMock1],
                ['el_pais', $newspaperMock2],
            ]);

        $this->feedsImporterMock
            ->expects($this->exactly(2))
            ->method('import')
            ->with($this->isInstanceOf(NewspaperInterface::class));

        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');

        $this->dailyHelperMock
            ->expects($this->once())
            ->method('today')
            ->willReturn($today);

        $this->dailyHelperMock
            ->expects($this->once())
            ->method('tomorrow')
            ->willReturn($tomorrow);

        $this->feedRepositoryMock
            ->expects($this->once())
            ->method('findNewspapersFeedsInRange')
            ->with(
                $this->equalTo(['el_mundo', 'el_pais']),
                $this->equalTo($today),
                $this->equalTo($tomorrow),
                $this->equalTo(15)
            )
            ->willThrowException(new MongoDBException('Database error'));

        $this->feedsHandler->getTrendingFeeds($inputNewspaperKeys);
    }

    public function testEmptyNewspaperKeys(): void
    {
        $inputNewspaperKeys = [];

        $this->newspaperFactoryMock
            ->expects($this->never())
            ->method('create');

        $this->feedsImporterMock
            ->expects($this->never())
            ->method('import');

        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');

        $this->dailyHelperMock
            ->expects($this->once())
            ->method('today')
            ->willReturn($today);

        $this->dailyHelperMock
            ->expects($this->once())
            ->method('tomorrow')
            ->willReturn($tomorrow);

        $expectedFeeds = [];

        $this->feedRepositoryMock
            ->expects($this->once())
            ->method('findNewspapersFeedsInRange')
            ->with(
                $this->equalTo([]),
                $this->equalTo($today),
                $this->equalTo($tomorrow),
                $this->equalTo(15)
            )
            ->willReturn($expectedFeeds);

        $result = $this->feedsHandler->getTrendingFeeds($inputNewspaperKeys);

        $this->assertSame($expectedFeeds, $result);
    }
}
