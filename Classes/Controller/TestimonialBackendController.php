<?php

declare(strict_types=1);

namespace NITSAN\NsTestimonial\Controller;
use NITSAN\NsTestimonial\NsConstantModule\ExtendedTemplateService;
use NITSAN\NsTestimonial\NsConstantModule\TypoScriptTemplateConstantEditorModuleFunctionController;
use NITSAN\NsTestimonial\NsConstantModule\TypoScriptTemplateModuleController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\Inject as inject;


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
class TestimonialBackendController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
     * testimonialRepository
     *
     * @var \NITSAN\NsTestimonial\Domain\Repository\TestimonialRepository
     */
    protected $testimonialBackendRepository = null;

    /**
     * @param \NITSAN\NsTestimonial\Domain\Repository\TestimonialBackendRepository $testimonialBackendRepository
     */
    public function injectTestimonialBackendRepository(\NITSAN\NsTestimonial\Domain\Repository\TestimonialBackendRepository $testimonialBackendRepository)
    {
        $this->testimonialBackendRepository = $testimonialBackendRepository;
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
     * @return void
     */
    public function listAction(): void
    {
        $bootstrapVariable = 'data';
        if (version_compare(TYPO3_branch, '11.0', '>')) {
            $bootstrapVariable = 'data-bs';
        }
        $testimonials = $this->testimonialRepository->findAll();
        $this->view->assign('testimonials', $testimonials);
        $assign = [
            'action' => 'list',
            'testimonials' => $testimonials,
            'bootstrapVariable' => $bootstrapVariable
        ];
        $this->view->assignMultiple($assign);
    }
    protected $templateService;
    protected $constantObj;
    protected $sidebarData;
    protected $dashboardSupportData;
    protected $generalFooterData;
    protected $premiumExtensionData;
    protected $constants;
    protected $contentObject = null;
    protected $pid = null;

    /**
     * @var TypoScriptTemplateModuleController
     */
    protected $pObj;

    /**
     * Initializes this object
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->contentObject = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
        $this->templateService = GeneralUtility::makeInstance(ExtendedTemplateService::class);
        $this->constantObj = GeneralUtility::makeInstance(TypoScriptTemplateConstantEditorModuleFunctionController::class);
    }

    /**
     * Initialize Action
     *
     * @return void
     */
    public function initializeAction()
    {
        parent::initializeAction();
        //Links for the All Dashboard VIEW from API...
        $sidebarUrl = 'https://composer.t3terminal.com/API/ExtBackendModuleAPI.php?extKey=ns_testimonial&blockName=DashboardRightSidebar';
        $dashboardSupportUrl = 'https://composer.t3terminal.com/API/ExtBackendModuleAPI.php?extKey=ns_testimonial&blockName=DashboardSupport';
        $generalFooterUrl = 'https://composer.t3terminal.com/API/ExtBackendModuleAPI.php?extKey=ns_testimonial&blockName=GeneralFooter';
        $premiumExtensionUrl = 'https://composer.t3terminal.com/API/ExtBackendModuleAPI.php?extKey=ns_testimonial&blockName=PremiumExtension';

        $this->testimonialRepository->deleteOldApiData();
        $checkApiData = $this->testimonialRepository->checkApiData();
        if (!$checkApiData) {
            $this->sidebarData = $this->testimonialRepository->curlInitCall($sidebarUrl);
            $this->dashboardSupportData = $this->testimonialRepository->curlInitCall($dashboardSupportUrl);
            $this->generalFooterData = $this->testimonialRepository->curlInitCall($generalFooterUrl);
            $this->premiumExtensionData = $this->testimonialRepository->curlInitCall($premiumExtensionUrl);

            $data = [
                'right_sidebar_html' => $this->sidebarData,
                'support_html'=> $this->dashboardSupportData,
                'footer_html' => $this->generalFooterData,
                'premuim_extension_html' => $this->premiumExtensionData,
                'extension_key' => 'ns_testimonial',
                'last_update' => date('Y-m-d')
            ];
            $this->testimonialRepository->insertNewData($data);
        } else {
            $this->sidebarData = $checkApiData['right_sidebar_html'];
            $this->dashboardSupportData = $checkApiData['support_html'];
            $this->premiumExtensionData = $checkApiData['premuim_extension_html'];
        }

        //GET CONSTANTs
        $this->constantObj->init($this->pObj);
        $this->constants = $this->constantObj->main();
    }

    /**
     * action commonSettings
     *
     * @return void
     */
    public function commonSettingsAction()
    {
        $bootstrapVariable = 'data';
        if (version_compare(TYPO3_branch, '11.0', '>')) {
            $bootstrapVariable = 'data-bs';
        }
        $assign = [
            'action' => 'commonSettings',
            'constant' => $this->constants,
            'bootstrapVariable' => $bootstrapVariable
        ];
        $this->view->assignMultiple($assign);
    }

    /**
     * action dashboard
     *
     * @return void
     */
    public function dashboardAction()
    {
        $testimonials = $this->testimonialRepository->findAll();
        // $totalImage = $this->nsMediaRepository->findAll();
        $report = isset($report) ? $report : "";
        $bootstrapVariable = 'data';
        if (version_compare(TYPO3_branch, '11.0', '>')) {
            $bootstrapVariable = 'data-bs';
        }
        $assign = [
            'action' => 'dashboard',
            'total' => count($testimonials),
            // 'totalImage' => count($totalImage),
            'pid' => $this->pid,
            'rightSide' => $this->sidebarData,
            'dashboardSupport' => $this->dashboardSupportData,
            'report' => $report,
            'bootstrapVariable' => $bootstrapVariable
        ];
        $this->view->assignMultiple($assign);
    }

    /**
     * action premiumExtension
     *
     * @return void
     */
    public function premiumExtensionAction()
    {
        $assign = [
            'action' => 'premiumExtension',
            'premiumExdata' => $this->premiumExtensionData
        ];
        $this->view->assignMultiple($assign);
    }

    /**
     * action saveConstant
     */
    public function saveConstantAction()
    {
        $this->constantObj->main();
        $returnAction = $_REQUEST['tx_nstestimonial_domain_model_testimonial']['__referrer']['@action']; //get action name
        return false;
    }
}
