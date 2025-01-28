<?php

namespace App\Tests\Service\Feeds;

use App\Document\Feed;
use App\Exception\FeedsImporterException;
use App\Repository\FeedRepository;
use App\Service\DailyHelper;
use App\Service\Feeds\FeedsImporter;
use App\Service\Newspaper\NewspaperInterface;
use Doctrine\ODM\MongoDB\MongoDBException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use DateTime;

class FeedsImporterTest extends TestCase
{
    private MockObject|FeedRepository $feedRepositoryMock;
    private MockObject|DailyHelper $dailyHelperMock;

    private FeedsImporter $feedsImporter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->feedRepositoryMock = $this->createMock(FeedRepository::class);
        $this->dailyHelperMock = $this->createMock(DailyHelper::class);

        $this->feedsImporter = new FeedsImporter(
            $this->feedRepositoryMock,
            $this->dailyHelperMock
        );
    }

    public function testImportWithValidFeedsAndNoExistingFeeds(): void
    {
        $newspaperMock = $this->createMock(NewspaperInterface::class);

        $feed1 = new Feed();
        $feed1->setTitle('Feed 1')
            ->setNewspaperKey('el_mundo')
            ->setPublishedAt(new DateTime('today'))
            ->setNewspaperName('El Mundo');

        $feed2 = new Feed();
        $feed2->setTitle('Feed 2')
            ->setNewspaperKey('el_pais')
            ->setPublishedAt(new DateTime('today'))
            ->setNewspaperName('El País');

        $newspaperMock->expects($this->once())
            ->method('getLastFeeds')
            ->willReturn([$feed1, $feed2]);

        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');

        $this->dailyHelperMock->expects($this->exactly(2))
            ->method('today')
            ->willReturn($today);

        $this->dailyHelperMock->expects($this->exactly(2))
            ->method('tomorrow')
            ->willReturn($tomorrow);

        $this->feedRepositoryMock->expects($this->exactly(2))
            ->method('findNewspaperFeedsInRange')
            ->willReturnMap([
                ['el_mundo', $today, $tomorrow, []],
                ['el_pais', $today, $tomorrow, []],
            ]);

        $savedFeeds = [];

        $this->feedRepositoryMock->expects($this->exactly(2))
            ->method('save')
            ->with($this->callback(function ($feed) use (&$savedFeeds, $feed1, $feed2) {
                $this->assertContains($feed, [$feed1, $feed2]);
                $savedFeeds[] = $feed;

                return true;
            }));

        $this->feedsImporter->import($newspaperMock);

        $this->assertSame([$feed1, $feed2], $savedFeeds);
    }

    public function testImportSkipsSavingWhenHasEnoughTodayFeeds(): void
    {
        $newspaperMock = $this->createMock(NewspaperInterface::class);

        $feed = new Feed();
        $feed->setTitle('Feed 1')
            ->setNewspaperKey('el_mundo')
            ->setPublishedAt(new DateTime('today'))
            ->setNewspaperName('El Mundo');

        $newspaperMock->expects($this->once())
            ->method('getLastFeeds')
            ->willReturn([$feed]);

        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');

        $this->dailyHelperMock->expects($this->once())
            ->method('today')
            ->willReturn($today);

        $this->dailyHelperMock->expects($this->once())
            ->method('tomorrow')
            ->willReturn($tomorrow);

        $this->feedRepositoryMock->expects($this->once())
            ->method('findNewspaperFeedsInRange')
            ->willReturnMap([
                ['el_mundo', $today, $tomorrow, [new Feed(), new Feed(), new Feed(), new Feed(), new Feed()]],
            ]);

        $this->feedRepositoryMock->expects($this->never())
            ->method('save');

        $this->feedsImporter->import($newspaperMock);
    }

    public function testImportThrowsExceptionWhenFeedIsInvalid(): void
    {
        $this->expectException(FeedsImporterException::class);
        $this->expectExceptionMessage("Invalid Feed");

        $newspaperMock = $this->createMock(NewspaperInterface::class);

        $invalidFeed = $this->createMock(\stdClass::class);

        $newspaperMock->expects($this->once())
            ->method('getLastFeeds')
            ->willReturn([$invalidFeed]);

        $this->feedsImporter->import($newspaperMock);
    }

    public function testImportHandlesRepositoryExceptionOnSave(): void
    {
        $this->expectException(MongoDBException::class);
        $this->expectExceptionMessage("Database error");

        $newspaperMock = $this->createMock(NewspaperInterface::class);

        $feed = new Feed();
        $feed->setTitle('Feed 1')
            ->setNewspaperKey('el_mundo')
            ->setPublishedAt(new DateTime('today'))
            ->setNewspaperName('El Mundo');

        $newspaperMock->expects($this->once())
            ->method('getLastFeeds')
            ->willReturn([$feed]);

        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');

        $this->dailyHelperMock->expects($this->once())
            ->method('today')
            ->willReturn($today);

        $this->dailyHelperMock->expects($this->once())
            ->method('tomorrow')
            ->willReturn($tomorrow);

        $this->feedRepositoryMock->expects($this->once())
            ->method('findNewspaperFeedsInRange')
            ->willReturnMap([
                ['el_mundo', $today, $tomorrow, []],
            ]);

        $this->feedRepositoryMock->expects($this->once())
            ->method('save')
            ->with($feed)
            ->willThrowException(new MongoDBException('Database error'));

        $this->feedsImporter->import($newspaperMock);
    }

    public function testImportWithEmptyFeeds(): void
    {
        $newspaperMock = $this->createMock(NewspaperInterface::class);

        $newspaperMock->expects($this->once())
            ->method('getLastFeeds')
            ->willReturn([]);

        $this->dailyHelperMock->expects($this->never())
            ->method('today');

        $this->dailyHelperMock->expects($this->never())
            ->method('tomorrow');

        $this->feedRepositoryMock->expects($this->never())
            ->method('findNewspaperFeedsInRange');

        $this->feedRepositoryMock->expects($this->never())
            ->method('save');

        $this->feedsImporter->import($newspaperMock);
    }
}
