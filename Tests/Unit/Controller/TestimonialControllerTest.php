<?php

declare(strict_types=1);

namespace NITSAN\NsTestimonial\Tests\Unit\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Test case
 *
 * @author HImasnhu <himanshu.nitsan@gmail.com>
 */
class TestimonialControllerTest extends UnitTestCase
{
    /**
     * @var \NITSAN\NsTestimonial\Controller\TestimonialController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\NITSAN\NsTestimonial\Controller\TestimonialController::class))
            ->onlyMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllTestimonialsFromRepositoryAndAssignsThemToView(): void
    {
        $allTestimonials = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $testimonialRepository = $this->getMockBuilder(\NITSAN\NsTestimonial\Domain\Repository\TestimonialRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $testimonialRepository->expects(self::once())->method('findAll')->will(self::returnValue($allTestimonials));
        $this->subject->_set('testimonialRepository', $testimonialRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('testimonials', $allTestimonials);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenTestimonialToView(): void
    {
        $testimonial = new \NITSAN\NsTestimonial\Domain\Model\Testimonial();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('testimonial', $testimonial);

        $this->subject->showAction($testimonial);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenTestimonialToTestimonialRepository(): void
    {
        $testimonial = new \NITSAN\NsTestimonial\Domain\Model\Testimonial();

        $testimonialRepository = $this->getMockBuilder(\NITSAN\NsTestimonial\Domain\Repository\TestimonialRepository::class)
            ->onlyMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $testimonialRepository->expects(self::once())->method('add')->with($testimonial);
        $this->subject->_set('testimonialRepository', $testimonialRepository);

        $this->subject->createAction($testimonial);
    }
}
