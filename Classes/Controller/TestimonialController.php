<?php

declare(strict_types=1);

namespace NITSAN\NsTestimonial\Controller;
use Doctrine\DBAL\Configuration;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * This file is part of the "Testimonial Showcase" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 HImasnhu <himanshu.nitsan@gmail.com>, NITSAN
 */

/**
 * TestimonialController
 */
class TestimonialController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * testimonialRepository
     *
     * @var \NITSAN\NsTestimonial\Domain\Repository\TestimonialRepository
     */
    protected $testimonialRepository = null;

    /**
     * @param \NITSAN\NsTestimonial\Domain\Repository\TestimonialRepository $testimonialRepository
     */
    public function injectTestimonialRepository(\NITSAN\NsTestimonial\Domain\Repository\TestimonialRepository $testimonialRepository)
    {
        $this->testimonialRepository = $testimonialRepository;
    }

    /**
     * action index
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function indexAction(): \Psr\Http\Message\ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {

        // $testimonials = $this->testimonialRepository->findWithStorageId((int)$this->settings['storagePid']);
        $testimonials = $this->testimonialRepository->findAll();
        $this->view->assign('testimonials', $testimonials);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \NITSAN\NsTestimonial\Domain\Model\Testimonial $testimonial
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(\NITSAN\NsTestimonial\Domain\Model\Testimonial $testimonial): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('testimonial', $testimonial);
        return $this->htmlResponse();
    }

    /**
     * action new
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function newAction(): \Psr\Http\Message\ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param \NITSAN\NsTestimonial\Domain\Model\Testimonial $newTestimonial
     */
    public function createAction(\NITSAN\NsTestimonial\Domain\Model\Testimonial $newTestimonial)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->testimonialRepository->add($newTestimonial);
        $this->redirect('list');
    }

    /**
     * action
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function Action(): \Psr\Http\Message\ResponseInterface
    {
        return $this->htmlResponse();
    }
}
